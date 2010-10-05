<?php

namespace {

// shortcut for translator function
function t($message) {
    static $translator;
    if (!$translator) $translator = Nette\Environment::getService('Nette\ITranslator');
    
    $args = func_get_args();
    return call_user_func_array(callback($translator, 'translate'), $args);
}

}


namespace Mantra {

use Nette\Templates\LatteMacros;
use Nette\Environment;


class TranslationHelper {
    
    private static $shards = array();
    
    public static function register() {
        // translation macro {t}message{/t}
        LatteMacros::$defaultMacros['t']  = '<?php Mantra\\TranslationHelper::begin(); ob_start(); echo \'';
        LatteMacros::$defaultMacros['/t'] = '\'; Mantra\\TranslationHelper::addShard(ob_get_clean()); echo Mantra\\TranslationHelper::end(); ?>';
        
        // translation parameter macro (allows including HTML and Latte code as params for translated strings)
        LatteMacros::$defaultMacros['tparam']  = '\'; Mantra\\TranslationHelper::addShard(ob_get_contents()); ob_clean(); ?>';
        LatteMacros::$defaultMacros['/tparam'] = '<?php Mantra\\TranslationHelper::addShard(ob_get_contents()); ob_clean(); echo \'';
    }
    
    public static function begin() {
        if (self::$shards) throw new \Exception("Translation macro {t} cannot be nested!");
    }
    
    public static function addShard($shard) {
        self::$shards[] = $shard;
    }
    
    public static function end() {
        static $translator;
        if (!$translator) $translator = Environment::getService('Nette\ITranslator');
        
        $translation = call_user_func_array(callback($translator, 'joinTranslate'), self::$shards);
        
        self::$shards = array();
        
        return $translation;
    }
    
}

}
