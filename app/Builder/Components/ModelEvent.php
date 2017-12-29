<?php

namespace App\Builder\Components;

use App\Builder\BuilderClass;

/**
 * Description of ModelEvents
 *
 * @author davidcallizaya
 */
class ModelEvent extends BuilderClass
{
    /**
     *
     * @var \App\Builder\PhpFunction $code
     */
    public $code;

    public function className()
    {
        return 'App\Events\\' . $this->owner->module() . '\\' . $this->eventName();
    }

    public function eventName()
    {
        return ucfirst(camel_case($this->owner->name() . '_' . $this->ownerIndex));
    }

    public function varName()
    {
        return '$' . $this->lcName();
    }

    public function lcName()
    {
        return camel_case($this->owner->name());
    }

    public function listenerName()
    {
        return $this->eventName() . 'Listener';
    }

    public function handle()
    {
        return $this->code->namedFunction('handle');
    }
}
