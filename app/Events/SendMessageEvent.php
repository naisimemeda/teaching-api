<?php

namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendMessageEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    public int $send_id;

    private int $receive_id;

    public string $send_type;

    public string $avatar_url;

    public string $name;

    public string $created_at;

    /**
     * SendMessageEvent constructor.
     * @param string $message 消息
     * @param int $send_id  发送者ID
     * @param string $send_type 发送者类型 student/teacher
     * @param Model $user
     * @param int $receive_id
     */
    public function __construct(string $message, int $send_id, string $send_type, Model $user, int $receive_id)
    {
        $this->message = $message;

        $this->send_id = $send_id;

        $this->send_type = $send_type;

        $this->receive_id = $receive_id;

        if ($user != null) {
            $this->avatar_url = $user->avatar_url;

            $this->name = $user->name;
        }

        $this->created_at = now()->toDateTimeString();
    }

    public function broadcastOn()
    {
        if ($this->send_type == AUTH_PROVIDER_TEACHER) {
            return [STUDENT_PRIVATE_CHANNEL_PREFIX . $this->receive_id];
        }
        return [TEACHER_PRIVATE_CHANNEL_PREFIX . $this->receive_id];
    }

    public function broadcastAs()
    {
        return 'chat';
    }
}
