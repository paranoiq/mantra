<?php

namespace Mantra;

use Nette\Object;


/**
 * Formats JSON value for user interface
 */
class Formater extends Object {
    
    /** @var string indent characters */
    public $indent = '  ';
    /** @var bool HTML formated output */
    public $html = FALSE;
    /** @var string prefix for element CSS classes */
    public $cssClassPrefix = 'val';
    
    
    /**
     * Whitespace or HTML formated JSON
     * 
     * @param mixed
     * @param bool
     * @param string
     * @param integer
     */
    public function formatJson($val) {
        $json = $this->_json($val, 1);
        
        // objects
        //$json = preg_replace('/^(\s*)<span class=\'[A-Za-z0-9_]+-object\'>\{\n(\s+)/m', '$1{ ', $json);
        //$json = preg_replace('/\n(\s*)\}/m', ' }', $json);
        
        // arrays
        $json = preg_replace('/^(\s*<span class=\'[A-Za-z0-9_]+-array\'>)\[\n(\s+)/m', '$1[ ', $json);
        $json = preg_replace('/(?<!\})\n(\s*)\]/m', ' ]', $json);
        $json = preg_replace('/^([\s\[]*)\[\n(\s+)/m', '$1[ ', $json);
        $json = preg_replace("#(</span>),(\s*)\n(\s*)(<span class='json-(string|number|bool)'>)#m", '$1, $4', $json);
        
        return $json;
    }
    
    
    /**
     * @author Vlasta Neubauer    
     * @author David Grudl
     * 
     * @param mixed
     * @param integer
     * @return string
     */
    private function _json($val, $depth = 1) {
        
        // indexed array
        if (is_array($val) && (!$val || array_keys($val) === range(0, count($val) - 1))) {
            $tmp = array();
            foreach ($val as $k => $v) {
                $tmp[] = $this->_json($v, $depth + 1);
            }
            if (!$tmp) return $this->wrapArray('');
            return $this->wrapArray("\n" . str_repeat($this->indent, $depth) 
                . implode(",\n" . str_repeat($this->indent, $depth), $tmp) 
                . "\n" . str_repeat($this->indent, $depth - 1));
        }
        
        // associative array
        if (is_array($val) || is_object($val)) {
            $tmp = array();
            foreach ($val as $k => $v) {
                $tmp[] = $this->wrapKey($this->_json((string)$k, $depth + 1)) . ': ' . $this->_json($v, $depth + 1);
            }
            if (!$tmp) return $this->wrapObject('');
            return $this->wrapObject(
                (($depth - 1) ? "\n" . str_repeat($this->indent, $depth) : '')
                . implode(",\n" . str_repeat($this->indent, $depth), $tmp) 
                /*. "\n" . str_repeat($this->indent, $depth - 1)*/);
        }
        
        if (is_string($val)) {
            $val = str_replace(array("\\", "\x00"), array("\\\\", "\\u0000"), $val); // due to bug #40915
            return $this->wrapString(addcslashes($val, "\x8\x9\xA\xC\xD/\""));
        }
        
        if (is_int($val) || is_float($val)) {
            return $this->wrapNumber(rtrim(rtrim(number_format($val, 13, '.', ''), '0'), '.'));
        }
        
        if (is_bool($val)) {
            return $val ? $this->wrapBool('true') : $this->wrapBool('false');
        }
        
        return $this->wrapBool('null');
    }
    
    
    /**#@+
     * HTML formating
     * 
     * @param string
     * @return string
     */
    private function wrapBool($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-bool'>$val</span>";
        return $val;
    }
    private function wrapNumber($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-number'>$val</span>";
        return $val;
    }
    private function wrapString($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-string'>&quot;" . htmlspecialchars($val) . "&quot;</span>";
        return "\"$val\"";
    }
    private function wrapKey($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-key'>$val</span>";
        return $val;
    }
    private function wrapDate($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-date'>$val</span>";
        return $val;
    }
    private function wrapId($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-id'>$val</span>";
        return $val;
    }
    private function wrapReference($val) {
        ///
        if ($this->html) return "<span class='$this->cssClassPrefix-reference'>" . htmlspecialchars($val) . "</span>";
        return $val;
    }
    private function wrapRegexp($val) {
        ///
        if ($this->html) return "<span class='$this->cssClassPrefix-regexp'>" . htmlspecialchars($val) . "</span>";
        return $val;
    }
    private function wrapCode($val) {
        ///
        if ($this->html) return "<span class='$this->cssClassPrefix-code'>" . htmlspecialchars($val) . "</span>";
        return $val;
    }
    private function wrapBinary($val) {
        ///
        if ($this->html) return "<span class='$this->cssClassPrefix-binary'>" . $val . "</span>";
        return $val;
    }
    private function wrapObject($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-object'>{ " . $val . " }</span>";
        return "{" . $val . "}";
    }
    private function wrapArray($val) {
        if ($this->html) return "<span class='$this->cssClassPrefix-array'>[" . $val . "]</span>";
        return "[$val]";
    }
    /**#@-*/
    
}
