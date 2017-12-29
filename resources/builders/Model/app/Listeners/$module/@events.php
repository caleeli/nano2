<?php
namespace App\Listeners\/*{$o->module()*/Module/*}*/;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\/*{$o->module()*/Module/*}*/\/*{$o->name()*/Model/*}*/;
use App\Events\/*{$o->module()*/Module/*}*/\/*{$e->eventName()*/ModelEvent/*}*/;

class /*{$e->listenerName()*/ModelListener/*}*/
{
    public /*{$e->varName()*/$tarea/*}*/ = null;
    public /*{$e->handle()*/function handle($event)
    {
    }/*}*/
}
