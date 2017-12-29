<?php

namespace App\Builder;

use StdClass;

/**
 * Description of CommentTemplate
 *
 * @author davidcallizaya
 */
class CommentTemplate
{
    const OPEN_TAG = '\/\*\{[^}]+?\*\/';
    const CLOSE_TAG = '\/\*\}\*\/';

    /**
     *
     * @var string $template
     */
    private $template = '';

    /**
     *
     * @var StdClass $parsed
     */
    private $parsed = null;

    public function __construct($template)
    {
        $this->template = $template;
        $this->parse();
    }

    private function parse()
    {
        preg_match_all(
            '/(' . self::OPEN_TAG . ')|(' . self::CLOSE_TAG . ')/',
            $this->template, $tags, PREG_OFFSET_CAPTURE
        );
        $prev = 0;
        $this->parsed = $this->createNode('root', [], null);
        $current = $this->parsed;
        foreach ($tags[0] as $i => $tag) {
            $pos = $tag[1];
            $text = substr($this->template, $prev, $pos - $prev);
            $current->content[] = $this->createNode(
                '$node->content', $text, $current
            );
            if ($tags[1][$i][1]>=0) {
                $node = $this->createNode(
                    substr($tag[0], 3, -2), [], $current
                );
                $current->content[] = $node;
                $current = $node;
            } else {
                $current = isset($current->parent) ? $current->parent : null;
            }
            $prev = $pos + strlen($tag[0]);
        }
        $text = substr($this->template, $prev);
        $current->content[] = $this->createNode('$node->content', $text, $current);
    }

    private function createNode($verb, $content, $parent)
    {
        $node = new StdClass;
        $node->verb = $verb;
        $node->content = $content;
        $node->parent = $parent;
        return $node;
    }

    public function evaluate($o, $e = null, $debug=false)
    {
        $result = [];
        foreach ($this->parsed->content as $node) {
            //if ($debug) dump($node);
            $res = $this->evaluateNode($node, $o, $e);
            if (is_array($res)) {
                foreach($res as $r) {
                    $result[]= $r;
                }
            } else {
                $result[]= $res;
            }
        }
        return $result;
    }

    public function evaluateArray($content, $o, $array)
    {
        $res = [];
        foreach ($array as $e) {
            foreach ($content as $node) {
                $res[] = $this->evaluateNode($node, $o, $e);
            }
        }
        return $res;
    }

    private function evaluateNode($node, $o, $e = null)
    {
        return $this->evaluateTag($node, $o, $e);
    }

    private function evaluateTag($node, $o, $e)
    {
        //dump($o);
        try {
            $res = eval('return ' . $node->verb . ';');
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage().': '.$node->verb, 0, $ex);
        }
        return $res;
    }
}
