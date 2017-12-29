<?php

namespace App\Builder;

use DomDocument;
use DOMElement;

/**
 * Description of Builder
 *
 * @author davidcallizaya
 */
class Builder
{
    /**
     *
     * @var StdClass $o
     */
    private $o;

    private $encodedObjects = [];

    /**
     * Build a nano file.
     */
    public function build($filename, $target)
    {
        $dom = $this->preprocess($filename);
        foreach ($dom->getElementsByTagName('script') as $node) {
            $type = $node->getAttribute("type");
            $this->buildComponent($type, $node, $target);
        }
    }

    /**
     *
     * @param type $filename
     * @return DomDocument
     */
    private function preprocess($filename)
    {
        $dom = new DomDocument;
        $code = file_get_contents($filename);
        $tokens = token_get_all($code);
        $phpCode = null;
        $xmlCode = '';
        foreach ($tokens as $t) {
            if (is_array($t)) {
                if (T_INLINE_HTML === $t[0]) {
                    $xmlCode.=$t[1];
                } elseif (T_OPEN_TAG === $t[0]) {
                    $phpCode = '';
                } elseif (T_CLOSE_TAG === $t[0]) {
                    if (substr($phpCode, 0, 4) === 'xml ') {
                        $xmlCode.="<?$phpCode?>";
                    } else {
                        $lines = substr_count($phpCode, "\n") + 1;
                        $xmlCode.=htmlentities($this->encodeObject(['code'=>new PhpFunction($phpCode)]), ENT_NOQUOTES, 'utf-8').str_repeat("\n", $lines);
                    }
                } else {
                    $phpCode.=$t[1];
                }
            } else {
                $phpCode.=$t;
            }
        }
        $dom->loadXML($xmlCode);
        return $dom;
    }

    private function encodeObject($object) {

        $key = array_search($object, $this->encodedObjects, true);
        if ($key === false) {
            $key = uniqid();
            $this->encodedObjects[$key] = $object;
        }
        return json_encode($key);
    }

    public function getEncoded($string)
    {
        $isEncoded = is_string($string) && isset($this->encodedObjects[$string]);
        return $isEncoded ? $this->encodedObjects[$string] : $string;
    }

    private function buildComponent($type, DOMElement $node, $target)
    {
        $json = json_decode($node->nodeValue);
        $class = 'App\Builder\Components\\' . $type;
        $this->o = new $class($json, $this, $this, null);
        $path = resource_path("builders/$type");
        $createFile = function($filename, CommentTemplate $template, $e, $p) {
            $content = $template->evaluate($this->o, $e);
            file_put_contents($filename, implode('', $content));
        };
        $createFolder = function($dirname, $template, $e, $p) use(&$createFile, &$createFolder) {
            mkdir($dirname, 0755, true);
            $this->glob($p, $createFile, $createFolder, $dirname);
        };
        $this->glob($path, $createFile, $createFolder, $target);
    }
    public function prepareClassBase($name)
    {
        $template = new CommentTemplate(file_get_contents(resource_path('builders/ClassName.php')));
        $this->o = new FakeClass();
        $path = resource_path("builders/$name");
        $createFile = function($filename, CommentTemplate $template = null, $e = null) {
            $content = $template->evaluate($this->o);
        };
        $createFolder = function($dirname, $template, $e = null) use(&$createFile, &$createFolder) {
            $this->glob($p, $createFile, $createFolder, $dirname);
        };
        $this->glob($path, $createFile, $createFolder);
        return $template->evaluate(new FakeBuilder($name, $this->o->__missing), null, true);
    }

    private function glob($path, $createFile, $createFolder, $dirbase)
    {
        foreach (glob("$path/*") as $p) {
            $name = basename($p);
            $nameParts = explode('.', $name, 2);
            $ext = isset($nameParts[1]) ? $nameParts[1] : '';
            $basename = $nameParts[0];
            $prefix = substr($name, 0, 1);
            if (is_file($p)) {
                $template = new CommentTemplate(file_get_contents($p));
                $create = $createFile;
            } else {
                $template = null;
                $create = $createFolder;
            }
            switch ($prefix) {
                case '$':
                    $method = substr($nameParts[0], 1);
                    $basename = $this->o->$method();
                    $create("$dirbase/$basename".($ext?".$ext":""), $template, null, $p);
                    break;
                case '@':
                    $method = substr($nameParts[0], 1);
                    $elements = $this->o->$method();
                    if ($elements) {
                        foreach ($elements as $basename => $e) {
                            $create("$dirbase/$basename".($ext?".$ext":""), $template, $e, $p);
                        }
                    }
                    break;
                default:
                    $create("$dirbase/$basename".($ext?".$ext":""), $template, null, $p);
            }
        }
    }
}
