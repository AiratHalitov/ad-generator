<?php

require_once dirname(__FILE__) . '/Node.php';

class Randomizer
{
    private $_text = '';

    private $_tree = null;

    public function __construct($text = '')
    {
        $text = (string) $text;
        $this->_text = $text;
        $this->_tree = new Node;
        $preg = '/
            \\\\\\\            | # мнемонизированный слэш
            \\\\\+             | # мнемонизированный +
            \\\\\{             | # мнемонизированный {
            \\\\\}             | # мнемонизированный }
            \\\\\[             | # мнемонизированный [
            \\\\\]             | # мнемонизированный ]
            \\\\\|             | # мнемонизированный |
            \\\                | # никчемный слэш
            \[\+               | # начало разделителя
            \+                 | # возможно, конец разделителя перетановок
            \{                 | # начало перебора
            \}                 | # конец перевора
            \[                 | # начало перестановки
            \]                 | # конец перестановки
            \|                 | # разделитель вариантов
            [^\\\+\{\}\[\]\|]+   # все прочее
            /xu';
        $currentNode = $this->_tree;
        $currentNode = new Node($currentNode);
        $currentNode->setType('series');
        $currentNode = $currentNode->concat('');
        while (preg_match($preg, $text, $match)) {
            switch ($match[0]) {
                case '\\\\':
                case '\\':
                    $currentNode = $currentNode->concat('\\');
                    break;
                case '\+':
                    $currentNode = $currentNode->concat('+');
                    break;
                case '\{':
                    $currentNode = $currentNode->concat('{');
                    break;
                case '\}':
                    $currentNode = $currentNode->concat('}');
                    break;
                case '\[':
                    $currentNode = $currentNode->concat('[');
                    break;
                case '\]':
                    $currentNode = $currentNode->concat(']');
                    break;
                case '\|':
                    $currentNode = $currentNode->concat('|');
                    break;
                case '[+':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Node($currentNode->parent);
                    } else {
                        $currentNode = new Node($currentNode);
                    }
                    $currentNode->isSeparator = true;
                    break;
                case '+':
                    if ($currentNode->isSeparator) {
                        $currentNode->isSeparator = false;
                        $currentNode = new Node($currentNode);
                        $currentNode->setType('series');
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('+');
                    }
                    break;
                case '{':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Node($currentNode->parent);
                    } else {
                        $currentNode = new Node($currentNode);
                    }
                    $currentNode->setType('synonyms');
                    $currentNode = new Node($currentNode);
                    $currentNode->setType('series');
                    $currentNode = $currentNode->concat('');
                    break;
                case '}':
                    $is = $currentNode->parent->parent;
                    if ($is && 'synonyms' == $is->type) {
                        $currentNode = $is->parent;
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('}');
                    }
                    break;
                case '[':
                    if ('string' == $currentNode->type) {
                        $currentNode = new Node($currentNode->parent);
                    } else {
                        $currentNode = new Node($currentNode);
                    }
                    $currentNode = new Node($currentNode);
                    $currentNode->setType('series');
                    $currentNode = $currentNode->concat('');
                    break;
                case ']':
                    $is = $currentNode->parent->parent;
                    if ($is && 'mixing' == $is->type && $is->parent) {
                        $currentNode = $is->parent;
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat(']');
                    }
                    break;
                case '|':
                    $is = $currentNode->parent;
                    if ($is && 'series' == $is->type) {
                        $currentNode = $is->parent;
                        $currentNode = new Node($currentNode);
                        $currentNode->setType('series');
                        $currentNode = $currentNode->concat('');
                    } else {
                        $currentNode = $currentNode->concat('|');
                    }
                    break;
                default:
                    $currentNode = $currentNode->concat($match[0]);
            }
            $text = substr($text, strlen($match[0]));
        }
    }

    public function getText()
    {
        return $this->_tree->getText();
    }

    public function numVariant()
    {
        return $this->_tree->numVariant();
    }
}