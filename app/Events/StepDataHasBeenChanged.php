<?php

namespace App\Events;

use App\Models\Building;
use App\Models\Step;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StepDataHasBeenChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Step
     */
    public $step;
    /**
     * @var Building
     */
    public $building;
    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Step $step
     * @param Building $building
     * @param User $user
     *
     * @return void
     */
    public function __construct(Step $step, Building $building, User $user)
    {
        $this->step = $step;
        $this->building = $building;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}