<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;

class CreatePostEvent extends BaseShouldBroadcastEvent
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('create-post');
    }
}
