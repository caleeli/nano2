<?php

namespace App\Builder;

/**
 * Description of BuilderClass
 *
 * @author davidcallizaya
 */
class BuilderClass
{
    protected static $cast = [];

    /**
     *
     * @var BuilderClass $owner
     */
    protected $owner;
    protected $ownerIndex;
    private $builder;

    public function __construct($json, $owner, Builder $builder, $ownerIndex)
    {
        //if (!is_array($json)) dd($json);
        $json = $builder->getEncoded($json);
        foreach ((array) $json as $k => $v) {
            if (isset(static::$cast[$k])) {
                $isArray = substr(static::$cast[$k], -2) === '[]';
                $class = $isArray ? substr(static::$cast[$k], 0, -2) : static::$cast[$k];
                if ($isArray) {
                    $this->$k = [];
                    foreach ($v as $k1 => $v1) {
                        $this->{$k}[$k1] = new $class($v1, $this, $builder, $k1);
                    }
                } else {
                    $this->$k = new $class($v, $this, $builder, null);
                }
            } else {
                $this->$k = $v;
            }
        }
        $this->owner = $owner;
        $this->ownerIndex = $ownerIndex;
        $this->builder = $builder;
    }

    protected function transform($inputArray, $key, $value, $default)
    {
        $res = [];
        foreach ($inputArray as $field) {
            if (empty($key)) {
                $res[] = isset($field->$value) ? $field->$value : $default;
            } else {
                $res[$field->$key] = isset($field->$value) ? $field->$value : $default;
            }
        }
        return $res;
    }

    public function encode($expression)
    {
        return php_encode($expression);
    }
}
