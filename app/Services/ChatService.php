<?php

namespace App\Services;

use App\Events\SendMessageEvent;
use App\Models\ChatLog;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class ChatService
{
    /**
     * @param Model $auth 发送者 Model
     * @param int $receive_id 接受者 id
     * @param string $message 发送信息
     * @param int $send_id 发送者 id
     * @param string $send_type 发送者 provider
     * @return array
     */
    public function sendMessage(
        Model $auth,
        int $receive_id,
        string $message,
        int $send_id,
        string $send_type
    ): array {
        ChatLog::query()->updateOrInsert([
            'student_id' => $send_type === AUTH_PROVIDER_TEACHER ? $receive_id : $send_id,
            'teacher_id' => $send_type === AUTH_PROVIDER_STUDENT ? $receive_id : $send_id
        ], ['created_at' => now()]);

        event(new SendMessageEvent($message, $send_id, $send_type, $auth, $receive_id));

        $send_map_receive = [
            AUTH_PROVIDER_TEACHER => AUTH_PROVIDER_STUDENT,
            AUTH_PROVIDER_STUDENT => AUTH_PROVIDER_TEACHER,
        ];

        $send_message = [
            'send_id' => $send_id,
            'send_type' => $send_type,
            'message' => $message,
            'receive_id' => $receive_id,
            'receive_type' => $send_map_receive[$send_type]
        ];

        Message::query()->create($send_message);

        $send_message['created_at'] = now()->toDateTimeString();
        $send_message['avatar_url'] = $auth->avatar_url ?? '';
        $send_message['name'] = $auth->name ?? '';

        return $send_message;
    }


    /**
     * 通知 Line 用户
     * @param string $message
     * @param array $line_id
     */
    public function lineNotification(string $message, ?array $line_id = null)
    {
        $httpClient = new CurlHTTPClient(config('app.line_channel'));

        $bot = new LINEBot($httpClient, ['channelSecret' => config('app.line_secret')]);

        $textMessageBuilder = new TextMessageBuilder($message);

        $ids = !is_null($line_id)
            ? [$line_id]
            : Teacher::query()
                ->whereNotNull('line_id')
                ->unionAll(Student::query()->whereNotNull('line_id')->select('line_id'))
                ->select('line_id')
                ->pluck('line_id')
                ->unique()
                ->toArray();

        $bot->multicast($ids, $textMessageBuilder);
    }
}
