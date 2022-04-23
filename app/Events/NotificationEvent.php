<?php

namespace App\Events;

use Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class NotificationEvent extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $account_detail;

    public function __construct($account_detail)
    {
        $this->account_detail = $account_detail;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return ['forumbot'];
    }

    /**
     * Specify broadcast events (corresponding to front-end events)
     * @return string
     */
    public function broadcastAs()
    {
        return 'my-event';
    }

    /**
     * Get the broadcast data, the default is the data of the public attribute of the broadcast
     */
    public function broadcastWith()
    {
        return ['account_detail' => $this->account_detail];
    }
}
