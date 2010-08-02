<?php

namespace Mantra;

use Nette\Environment;
use Nette\ITranslator;

class Translator implements ITranslator {
    
    private $language;
    private $translations;
    
    
    public function translate($message, $count = NULL) {
        if ($this->translations === NULL) $this->loadTranslations();
        
        if (!isset($this->translations[$message])) return $message;
        return $this->translations[$message];
    }
    
    
    public function setLanguage($language) {
        if (!Language::isAvailable($language))
            throw new Exception("Language '$language' is not available.");
        
        $this->language = $language;
    }
    
    
    public function setTranslations($translations) {
        if (!is_array($translations) && !($translations instanceof ArrayAccess)) 
            throw new Exception("Translations must be an array or array-like object.");
        
        $this->translations = $translations;
    }
    
    
    private function loadTranslations() {
        if ($this->language === NULL) $this->language = Language::detectLanguage();
        
        include APP_DIR . '/lang/' . $this->language . '.lang.php';
        $this->translations = $translations;
    }
    
}



