<?php

namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class sendMessageEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $send_id;

    private $receive_id;

    public $send_type;

    public $avatar_url;

    public $name;

    public $created_at;

    /**
     * sendMessageEvent constructor.
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
        if ($this->send_type == 'teacher') {
            return ['private-student.' . $this->receive_id];
        }
        return ['private-teacher.' . $this->receive_id];
    }

    public function broadcastAs()
    {
        return 'chat';
    }
}
