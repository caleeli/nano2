<?php

namespace App\Builder;

/**
 * Description of FakeBuilder
 *
 * @author davidcallizaya
 */
class FakeBuilder
{
    private $missing;
    private $name;

    public function __construct($name, $missing)
    {
        $this->name = $name;
        $this->missing = $missing;
    }

    /**
     * name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function methods($node, $self)
    {
        $array = [];
        foreach ($this->missing as $key => $miss) {
            $array[] = '    /**
     * ' . $key . '.
     *
     * @return ' . (isset($miss->__missing['__toString']) ? 'string' : 'array') . '
     */
    public function ' . $key . '()
    {
        return $this->' . $key . ';
    }
';
        }
        return $array;
    }
}
