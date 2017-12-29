<?php

namespace App\Builder\Components;

use App\Builder\BuilderClass;

/**
 * Description of ModelRelationship
 *
 * @author davidcallizaya
 */
class ModelRelationship extends BuilderClass
{

    public function name()
    {
        return $this->name;
    }

    public function type()
    {
        return $this->type;
    }

    public function related()
    {
        $parts = explode('.', $this->model);
        if (count($parts) === 1) {
            $related = Model::ccClass($this->owner->module(), $parts[0]);
        }
        if (count($parts) === 2) {
            $related = Model::ccClass($parts[0], $parts[1]);
        }
        return $related;
    }

    public function foreignKey()
    {
        return isset($this->foreignKey) ? $this->foreignKey : null;
    }

    public function localKey()
    {
        return isset($this->localKey) ? $this->localKey : null;
    }
}
