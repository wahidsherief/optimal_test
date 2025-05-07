<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class NotifyEvent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $data;
    public $channel;

    public function __construct(array $data, string $channel)
    {
        $this->data = $data;
        $this->channel = $channel;
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->channel);
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
