<?php

namespace App\Services;

use App\Events\sendMessageEvent;
use App\Models\ChatLog;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class chatService
{
    /**
     * @param string $message
     * @param int $send_id
     * @param string $send_type
     * @param Model $auth
     * @param int $receive_id
     * @return array
     */
    public function sendMessage(
        string $message,
        int $send_id,
        string $send_type,
        Model $auth,
        int $receive_id
    ): array
    {
        ChatLog::query()->updateOrInsert([
            'student_id' => $send_type === 'teacher' ? $receive_id : $send_id,
            'teacher_id' => $send_type === 'student' ? $receive_id : $send_id
        ], ['created_at' => now()]);

        event(new sendMessageEvent($message, $send_id, $send_type, $auth, $receive_id));

        $send_message = [
            'send_id' => $send_id,
            'send_type' => $send_type,
            'message' => $message,
            'receive_id' => $receive_id,
            'receive_type' => $send_type === 'teacher' ? 'student' : 'teacher'
        ];

        Message::query()->create($send_message);

        $send_message['created_at'] = now()->toDateTimeString();
        $send_message['avatar_url'] = $auth->avatar_url ?? '';
        $send_message['name'] = $auth->name ?? '';

        return $send_message;
    }


    public function lineNotification(string $message, $line_id = 0)
    {
        $httpClient = new CurlHTTPClient(config('app.line_channel'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('app.line_secret')]);
        $textMessageBuilder = new TextMessageBuilder($message);
        if ($line_id == 0) {
            $teacher_line_ids = Teacher::query()->whereNotNull('line_id')->select('line_id')->pluck('line_id');
            $student_line_ids = Student::query()->whereNotNull('line_id')->select('line_id')->pluck('line_id');
            $ids = $teacher_line_ids->merge($student_line_ids)->unique()->toArray();
        } else {
            $ids[] = $line_id;
        }
        $bot->multicast($ids, $textMessageBuilder);
    }
}
