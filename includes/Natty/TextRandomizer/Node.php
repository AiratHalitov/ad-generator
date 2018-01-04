<?php
/**
 * Project:     Natty CMS: a PHP-based Content Management System
 * File:        Natty/TextRandomizer/Node.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @link http://xbb.uz/
 * @author Dmitriy Skorobogatov <info at xbb dot uz>
 * @version 0.21
 * @copyright 2006-2009 Dmitriy Skorobogatov
 * @package Natty
 */

class Natty_TextRandomizer_Node
{
    private $_parent = null;

    private $_str = '';

    private $_type = 'mixing';

    private $_subNodes = array();

    private $_usedKeys = array();

    private $_separator = '';

    private $_isSeparator = false;

    public function __construct(Natty_TextRandomizer_Node $parent = null)
    {
        $this->_parent = $parent;
        if ($parent) {
            $parent->_subNodes[] = $this;
        }
    }

    public function getText()
    {
        $result = '';
        switch ($this->_type) {
            case 'synonyms':
                if (! count($this->_usedKeys)) {
                    $this->_usedKeys = array_keys($this->_subNodes);
                }
                $key = array_rand($this->_usedKeys);
                $result = $this->_subNodes[$key]->getText();
                unset($this->_usedKeys[$key]);
                break;
            case 'mixing':
                shuffle($this->_subNodes);
                foreach ($this->_subNodes as $v) {
                    if ($result) {
                        $result .= ' ' . $this->_separator;
                    }
                    $result .= ' ' . $v->getText();
                }
                break;
            case 'series':
                foreach ($this->_subNodes as $v) {
                    $result .= ' ' . $v->getText();
                }
                break;
            default:
                $result = $this->_str;
        }
        //$result = trim($result);
        //$result = preg_replace('/\s+/u', ' ', $result);
        $result = preg_replace('| +|', ' ', $result);
        $result = str_replace('\\\\', '\\', $result);
        $result = str_replace(' ,', ',', $result);
        $result = str_replace(' .', '.', $result);
        $result = str_replace(' !', '!', $result);
        $result = str_replace(' ?', '?', $result);
        return $result;
    }

    public function numVariant()
    {
        $result = 1;
        switch ($this->_type) {
            case 'synonyms':
                $result = 0;
                foreach ($this->_subNodes as $v) {
                    $result += $v->numVariant();
                }
                break;
            case 'mixing':
                for ($i=2; $i<=count($this->_subNodes); ++$i) {
                    $result *= $i;
                }
                foreach ($this->_subNodes as $v) {
                    $result *= $v->numVariant();
                }
                break;
            case 'series':
                foreach ($this->_subNodes as $v) {
                    $result *= $v->numVariant();
                }
                break;
        }
        return $result;
    }

    public function concat($str)
    {
        $str = (string) $str;
        if ($this->_isSeparator) {
            $this->_separator .= $str;
            return $this;
        }
        if ('string' == $this->type) {
            $this->_str .= $str;
            return $this;
        }
        $currentNode = new Natty_TextRandomizer_Node($this);
        $currentNode->setType('string');
        return $currentNode->concat($str);
    }

    public function setType($type)
    {
        switch ((string) $type) {
            case 'string':
                $this->_type = 'string';
                break;
            case 'synonyms':
                $this->_type = 'synonyms';
                break;
            case 'series':
                $this->_type = 'series';
                break;
            default:
                $this->_type = 'mixing';
        }
    }

    public function __get($var) {
        $var = strtolower($var);
        switch ((string) $var) {
            case 'isseparator':
                return $this->_isSeparator;
                break;
            case 'parent':
                return $this->_parent;
                break;
            case 'type':
                return $this->_type;
                break;
            default:
                return null;
        }
    }

    public function __set($var, $value)
    {
        $var = strtolower($var);
        switch ((string) $var) {
            case 'isseparator':
                $this->_isSeparator = (boolean) $value;
        }
    }
}
