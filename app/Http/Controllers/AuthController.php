<?php

namespace App\Http\Controllers;

use App\Http\Kernel;
use App\Http\Requests\AuthRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\LineService;
use App\Traits\PassportToken;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use Psr\Http\Message\ResponseInterface;

class AuthController extends Controller
{
    use PassportToken;

    /**
     * 教师注册
     * @param AuthRequest $request
     * @return mixed
     * @throws \Throwable
     */
    public function store(AuthRequest $request)
    {
        $token = DB::transaction(function () use ($request) {
            $avatar = collect(Student::$avatars)->random();
            $data = array_merge($request->only(['email', 'password', 'name']), ['avatar_url' => $avatar, 'line_avatar_url' => $avatar ]);
            $teacher = Teacher::query()->create($data);
            return $this->getBearerTokenByUser($teacher, config('app.passport_client_id'), false, AUTH_PROVIDER_TEACHER);
        });
        return $this->success($token);
    }

    /**
     * 获取令牌
     * @param AuthRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function login(AuthRequest $request)
    {
        try {
            $url = request()->root() . '/oauth/token';

            $params = array_merge(config('passport.proxy'), [
                'username' => $request->get('email'),
                'password' => $request->get('password'),
                'provider' => $request->get('provider'),
            ]);

            $request = Request::create($url, 'POST', $params);

            return app(Kernel::class)->handle($request);
        } catch (RequestException $exception) {
            return $this->failed('账号或密码错误');
        }
    }

    /**
     * 获取绑定 Line 时的 Token
     * @param Request $request
     * @return mixed
     */
    public function getLineBindToken(Request $request)
    {
        $key = Str::random(10);

        Cache::put($key, Auth::id(), 600);

        return $this->success(['key' => $key]);
    }

    /**
     * line 登陆
     * @param Request $request
     * @return mixed
     */
    public function line(Request $request)
    {
        $bind_type = $request->get('type', 'binding');

        if ($bind_type === 'binding') {

            $key = $request->get('key');

            if (!$id = Cache::pull($key)) {
                return $this->failed('邀请码已过期');
            }

            $provider = $request->get('bind_type', 'teacher');

            $query = $this->getQuery($provider);

            $query->findOrFail($id);

            $request->session()->put('id', $id);

            $request->session()->put('bind_type', $provider);

        }


        $request->session()->put('type', $bind_type);

        return Socialite::with('line')->redirect();
    }


    /**
     * 用户登录回调
     * @param Request $request
     * @param LineService $lineService
     * @return RedirectResponse|Redirector
     */
    public function callback(Request $request, LineService $lineService)
    {
        $user = Socialite::driver('line')->user();
        $accessTokenResponseBody = $user->accessTokenResponseBody;

        if (!isset($accessTokenResponseBody['access_token'])) {
            $this->failed('认证失败', 404);
        }

        $user_profile = $lineService->getUserProfile($accessTokenResponseBody['access_token']);
        if (empty($user_profile)) {
            $this->failed('认证失败', 404);
        }

        $bind_type = $request->session()->get('bind_type');

        switch ($bind_type) {
            case 'teacher':
                $teacher = Teacher::query()->where('line_id', $user_profile['userId'])->first();
                if (!$teacher) {
                    $query = Teacher::query()->find($request->session()->get('id'));
                }
                break;
            case 'student':
                $query = Student::query()->find($request->session()->get('id'));
                break;
        }

        if (isset($query)) {
            $query->update([
                'line_name' => $user_profile['displayName'],
                'line_avatar_url' => $user_profile['pictureUrl'],
                'avatar_url' => $user_profile['pictureUrl'],
                'line_id' => $user_profile['userId']
            ]);
        }

        if ($request->session()->get('type') === 'binding') {
            return redirect(config('app.web_url') . "#/");
        }

        $key = Str::random();

        $data = [
            'teacher' => Teacher::query()->where('line_id', $user_profile['userId'])->first(),
            'student' => Student::query()->where('line_id', $user_profile['userId'])->get()
        ];

        Cache::put($key, json_encode($data, true), 30);

        return redirect(config('app.web_url') . "#/provider?key=$key");
    }


    /**
     * 获取 line 绑定的 teacher and student
     * @param Request $request
     * @return mixed
     */
    public function lineAccountList(Request $request)
    {
        $account = Cache::pull($request->get('key'));

        if (!$account) {
            return $this->failed('未绑定 Line ');
        }

        $oauth_key = Str::random();

        Cache::put($oauth_key, true, 5);

        return $this->success([
            'account' => json_decode($account, true),
            'oauth_key' => $oauth_key
        ]);
    }

    /**
     * line 第三方登陆
     * @param AuthRequest $request
     * @return AuthController|mixed|ResponseInterface
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function lineAuth(AuthRequest $request)
    {
        $oauth = Cache::pull($request->get('oauth_key'));

        if (!$oauth) {
            return $this->failed('认证错误');
        }

        $provider = $request->get('provider');

        switch ($request->get('provider')) {
            case AUTH_PROVIDER_TEACHER:
                $auth = Teacher::query()->find($request->get('id'));
                break;
            case AUTH_PROVIDER_STUDENT:
                $auth = Student::query()->find($request->get('id'));
                break;
        }

        return $this->getBearerTokenByUser($auth, 1, false, $provider);
    }

    /**
     * 验证是否允许加入私人频道
     * @param Request $request
     * @return JsonResponse
     */
    public function validNotification(Request $request)
    {
        $secret = config('broadcasting.connections.pusher.secret');

        $string_to_sign = $request->get('socket_id') . ':' . $request->get('channel_name');

        $signature = hash_hmac('sha256', $string_to_sign, $secret);

        $auth = config('broadcasting.connections.pusher.key') . ':' . $signature;

        return response()->json(compact('auth'));
    }

    private function getQuery(string $provider): Builder
    {

        switch ($provider) {
            case AUTH_PROVIDER_TEACHER:
                return Teacher::query();
            case AUTH_PROVIDER_STUDENT:
                return Student::query();
        }

    }
}
