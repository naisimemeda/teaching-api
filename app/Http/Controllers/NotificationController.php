<?php

namespace App\Http\Controllers;

use App\Events\PublicEvent;
use App\Events\SendMessageEvent;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class NotificationController extends Controller
{
    public function sendNotification(Request $request, ChatService $service)
    {
        $message = $request->get('messages');
        if (!$message) {
            admin_toastr('请填写推送信息', 'error');
            return back();
        }

        try {
            event(new PublicEvent($message));
            $service->lineNotification($message);
            admin_toastr('推送信息成功', 'success');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            admin_toastr('推送信息失败', 'error');
        }
        return back();
    }
}
