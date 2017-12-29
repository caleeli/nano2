<?php

namespace App\Builder;

/**
 * Description of FakeClass
 *
 * @author davidcallizaya
 */
class FakeClass implements \ArrayAccess, \Iterator
{
    public $__arguments = [];
    public $__missing = [];

    public function __call($name, $arguments)
    {
        $this->__missing[$name] = new FakeClass();
        foreach($arguments as $a) {
            $type = gettype($a);
            $type = $type!=='object' ?: preg_replace('/^.*'.'\\\\'.'(\w+)$/', get_class($a), '$1');
            $this->__missing[$name]->__arguments[] = $type;
        }
        return $this->__missing[$name];
    }

    public function __toString()
    {
        $this->__missing['__toString'] = '';
        return '';
    }

    public function offsetExists($offset)
    {
        return false;
    }

    public function offsetGet($offset)
    {
        return null;
    }

    public function offsetSet($offset, $value)
    {
        
    }

    public function offsetUnset($offset)
    {

    }

    public function current()
    {
        return null;
    }

    public function key()
    {
        return 0;
    }

    public function next()
    {
        return null;
    }

    public function rewind()
    {
        
    }

    public function valid()
    {
        return false;
    }
}
