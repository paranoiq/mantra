<?php

namespace Mantra;

use Nette\Environment;
use DirectoryIterator;


class Language {
    
    private static $language;
    private static $languages = array();
    private static $descriptions = array(
        'en' => 'English',
        'cs' => 'Čeština',
        /// etc.
    );
    
    public static function setLanguage($language) {
        self::$language = $language;
        $session = Environment::getSession('default');
        $session->language = $language;
        bar($session->language);
        Environment::getHttpResponse()->setCookie('language', $language, 1209600); // 14 days
    }
    
    public static function detectLanguage() {
        if (self::$language) return self::$language;
        
        $session = Environment::getSession('default');
        if (isset($session->language)) {
            self::setLanguage($session->language);
            return self::$language;
        }
        
       $cookies = Environment::getHttpRequest()->getCookies();
        if (isset($cookies['language']) && self::isAvailable($cookies['language'])) {
            self::setLanguage($cookies['language']);
            return self::$language;
        }
        
        $request = Environment::getHttpRequest();
        $language = $request->detectLanguage(self::getAvailableLanguages());
        if ($language) {
            self::setLanguage($language);
            return self::$language;
        }
        
        return 'en';
    }
    
    
    public static function getAvailableLanguages() {
        if (self::$languages) return self::$languages;
        
        $session = Environment::getSession('default');
        if (isset($session->languages)) {
            self::$languages = $session->languages;
            return self::$languages;
        }
        
        try {
            $dir = new DirectoryIterator(APP_DIR . '/lang');
        } catch (Exception $e) {
            throw new Exception("Cannot open translations directory '" . APP_DIR . "/lang'.");
        }
        foreach ($dir as $file) {
            if (!$file->isFile()) continue;
            if (!$file->isReadable()) continue;
            
            $language = substr($file->getFilename(), 0, 2);
            self::$languages[$language] = self::getDescription($language);
        }
        
        if (!self::$languages)
            throw new Exception("Translation files are missing.");
        
        return self::$languages;
    }
    
    
    public static function isAvailable($language) {
        return array_key_exists($language, self::getAvailableLanguages());
    }
    
    
    public static function getDescription($language) {
        return self::$descriptions[$language];
    }
    
}
