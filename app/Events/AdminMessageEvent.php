<?php

namespace App\Events;

use App\Http\Controllers\AuthController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AdminMessageEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    private int $receive_id;

    public string $send_type;

    public string $receive_type;

    public string $created_at;

    /**
     * SendMessageEvent constructor.
     * @param string $message 消息
     * @param string $send_type
     * @param string $receive_type
     * @param int $receive_id
     */
    public function __construct(string $message, string $send_type, string $receive_type, int $receive_id)
    {
        $this->message = $message;

        $this->send_type = $send_type;

        $this->receive_type = $receive_type;

        $this->receive_id = $receive_id;

        $this->created_at = now()->toDateTimeString();
    }

    public function broadcastOn()
    {
        if ($this->receive_type == AUTH_PROVIDER_TEACHER) {
            return [TEACHER_PRIVATE_CHANNEL_PREFIX . $this->receive_id];
        }
        return [STUDENT_PRIVATE_CHANNEL_PREFIX . $this->receive_id];
    }

    public function broadcastAs()
    {
        return 'chat';
    }
}
