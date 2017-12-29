<?php

namespace App\Builder\Components;

use App\Builder\BuilderClass;
use App\Builder\CommentTemplate;

class Model extends BuilderClass
{
    protected static $cast = [
        'relationships' => ModelRelationship::class . '[]',
        'events' => ModelEvent::class . '[]',
    ];

    /**
     * module.
     *
     * @return array
     */
    public function module()
    {
        return $this->module;
    }

    /**
     * events.
     *
     * @return array
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * name.
     *
     * @return array
     */
    public function name()
    {
        return static::ccName($this->name);
    }

    /**
     * table.
     *
     * @return array
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * fillable.
     *
     * @return array
     */
    public function fillable()
    {
        return indent($this->encode($this->transform($this->fields, null, 'name', null)));
    }

    /**
     * attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return indent($this->encode($this->transform($this->fields, 'name', 'default', null)));
    }

    /**
     * casts.
     *
     * @return array
     */
    public function casts()
    {
        return indent($this->encode($this->transform($this->fields, 'name', 'type', 'string')));
    }

    /**
     * relationships.
     *
     * @return array
     */
    public function relationships($node, CommentTemplate $builder)
    {
        return $builder->evaluateArray($node->content, $this, $this->relationships);
    }

    /**
     * listeners.
     *
     * @return array
     */
    public function eventsList()
    {
        /* @var $event ModelEvent */
        $res = [];
        foreach($this->events as $name => $event) {
            $res[$name] = $event->className();
        }
        return indent($this->encode($res));
    }

    /**
     * methods.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * url.
     *
     * @return array
     */
    public function url()
    {
        return '/api/' . $this->module . '/' . str_plural(snake_case($this->name));
    }

    /**
     * type.
     *
     * @return array
     */
    public function type()
    {
        return static::ccName($this->module) . '.' . static::ccName($this->name);
    }

    /**
     * jsonDefinition.
     *
     * @return json
     */
    public function jsonDefinition()
    {
        $res = [
            "attributes"    => [],
            "relationships" => [],
        ];
        foreach ($this->fields as $field) {
            $res["attributes"][$field->name] = $field;
        }
        foreach ($this->relationships as $relationship) {
            $res["relationships"][$relationship->name] = $relationship;
        }
        return json_encode($res);
    }

    /**
     * selection.
     *
     * @return array
     */
    public function selection()
    {
        return eval('return ('.$this->fillable().');');
    }

    public static function ccName($name)
    {
        return ucfirst(camel_case($name));
    }

    public static function ccClass($module, $name)
    {
        return 'App\Models\\' . static::ccName($module) . '\\' . static::ccName($name);
    }
}
