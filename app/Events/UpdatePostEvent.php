<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;

class UpdatePostEvent extends BaseShouldBroadcastEvent
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('update-post');
    }
}
