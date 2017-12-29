<?php

namespace App\Builder;

/**
 * Description of PhpFunction
 *
 * @author davidcallizaya
 */
class PhpFunction
{
    private $code;

    public function __construct($code)
    {
        $this->code = trim($code);
    }

    public function namedFunction($name)
    {
        return "function $name" . trim(substr($this->code, 8));
    }

    public function anonymousFunction($name)
    {
        return $this->code;
    }
}
