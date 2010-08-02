<?php

namespace Mantra;

use Nette\Environment;

class Language {
    
    public static function detectLanguage() {
        static $language;
        if ($language) return $language;
        
        $session = Environment::getSession('default');
        if (isset($session->language)) {
            $language = $session->language;
            return $language;
        }
        
//        $cookies = \Nette\Environment::getHttpRequest()->getCookies();
//         if (isset($cookies['language']) && self::isAvailable($cookies['language'])) {
//             $language = $cookies['language'];
//             $session['language'] = $language;
//             return $language;
//         }
        
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($languages as $ln) {
                $ln = substr($ln, 0, 2);
                if (self::isAvailable($ln)) {
                    $language = $ln;
                    $session->language = $language;
//                     \Nette\Environment::getHttpResponse()->setCookie('language', $language, 1209600); // 14 days
                    return $language;
                }
            }
        }
        
        return 'en';
    }
    
    
    public static function getAvailableLanguages() {
        static $languages = array();
        if ($languages) return $languages;
        
        $session = Environment::getSession('default');
        if (isset($session->languages)) {
            $languages = $session->languages;
            return $languages;
        }
        
        try {
            $dir = new \DirectoryIterator(APP_DIR . '/lang');
        } catch (Exception $e) { 
            throw new Exception("Cannot open translations directory '" . APP_DIR . "/lang'.");
        }
        foreach ($dir as $file) {
            if (!$file->isFile()) continue;
            if (!$file->isReadable()) continue;
            
            $language = substr($file->getFilename(), 0, 2);
            $languages[$language] = self::getDescription($language);
        }
        
        if (!$languages)
            throw new Exception("Translation files are missing.");
        
        return $languages;
    }
    
    
    public static function isAvailable($language) {
        return array_key_exists($language, self::getAvailableLanguages());
    }
    
    
    public static function getDescription($language) {
        static $descriptions = array(
            'en' => 'English',
            'cs' => 'Čeština',
            /// etc.
        );
        
        return $descriptions[$language];
    }
    
}
