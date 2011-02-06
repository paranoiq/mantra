<?php

namespace Mantra;

use Nette\Environment;
use Nette\ITranslator;

class Translator /*implements ITranslator*/ {
    
    private $language;
    private $translations;
    
    private $params;
    
    
    public function translate($message, $count = NULL) {
        if ($this->translations === NULL) $this->loadTranslations();
        
        if (!isset($this->translations[$message])) return $message;
        
        $message = $this->translations[$message];
        
        $params = func_get_args();
        array_shift($params);
        $message = preg_replace_callback('/(?<!%)%(?!%)/', 
            function () use ($params) { if (!$params) return '%'; return array_shift($params); }, $message);
        
        return str_replace('%%', '%', $message);
    }
    
    
    public function joinTranslate($shards) {
        $shards = func_get_args();
        
        if (count($shards) == 1) 
            return $this->translate($shards[0]);
        
        $odd = TRUE;
        $message = '';
        $params = array();
        foreach ($shards as $shard) {
            if ($odd) {
                $message .= $shard;
            } else {
                $message .= '%';
                $params[] = $shard;
            }
            $odd = !$odd;
        }
        
        $message = preg_replace_callback('/(?<!%)%(?!%)/', 
            function () use ($params) { if (!$params) return '%'; return array_shift($params); }, $this->translate($message));
        
        return str_replace('%%', '%', $message);
    }
    
    
    public function setLanguage($language) {
        if (!Language::isAvailable($language))
            throw new Exception("Language '$language' is not available.");
        
        $this->language = $language;
    }
    
    
    public function setTranslations($translations) {
        if (!is_array($translations) && !($translations instanceof ArrayAccess)) 
            throw new Exception("Translations must be an array or object implementing ArrayAccess.");
        
        $this->translations = $translations;
    }
    
    
    public function loadTranslations() {
        if ($this->language === NULL) $this->language = Language::detectLanguage();
        
        include APP_DIR . '/lang/' . $this->language . '.lang.php';
        $this->translations = $translations;
    }
    
}



