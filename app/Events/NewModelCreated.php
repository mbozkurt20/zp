<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewModelCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $model;

    /**
     * Create a new event instance.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function broadcastOn()
    {
        return new Channel('basket-channel'); // örneğin "model-channel"
    }

    public function broadcastAs()
    {
        return 'model.created';
    }
}
