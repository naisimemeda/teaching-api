<?php

namespace App\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class adminMessageEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    private $receive_id;

    public $send_type;

    public $receive_type;
    public $created_at;

    /**
     * sendMessageEvent constructor.
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
        if ($this->receive_type == 'teacher') {
            return ['private-teacher.' . $this->receive_id];
        }
        return ['private-student.' . $this->receive_id];
    }

    public function broadcastAs()
    {
        return 'chat';
    }
}
