<?php
namespace App\Events\/*{$o->module()*/Module/*}*/;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\/*{$o->module()*/Module/*}*/\/*{$o->name()*/Model/*}*/;

class /*{$e->eventName()*/ModelEvent/*}*/ implements ShouldBroadcast
{
    use InteractsWithSockets;
    public /*{$e->varName()*/$tarea/*}*/ = null;
    public function __construct(/*{$o->name()*/Tarea/*}*/ /*{$e->varName()*/$tarea/*}*/)
    {
        $this->/*{$e->lcName()*/tarea/*}*/ = /*{$e->varName()*/$tarea/*}*/;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
