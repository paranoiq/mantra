<?php

require_once LIBS_DIR . '/Nette/loader.php';
//require_once dirname(__FILE__) . '/functions.php';

use Nette\Debug;
use Nette\Environment;
use Nette\Application\Route;
use Nette\Application\SimpleRouter;

Debug::enable();
Debug::$strictMode = TRUE;

Environment::loadConfig();

Nella\VersionPanel::register();
Nella\CallbackPanel::register();

$session = Environment::getSession();
//$session->setSavePath(WWW_DIR . '/sessions/');

$application = Environment::getApplication();
//$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;

$router = $application->getRouter();

$router[] = new Route('index.php', array(
	'presenter' => 'Home',
	'database' => NULL,
	'collection' => NULL,
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<database>/<collection>', array(
	'presenter' => 'Home',
	'database' => NULL,
	'collection' => NULL,
	'action' => 'default',
));


///
require_once LIBS_DIR . /**/ '/../../../../phongo' . /**/ '/phongo/phongo.php';


Nette\Templates\LatteMacros::$defaultMacros['t'] = '<?php echo tt(\'';
Nette\Templates\LatteMacros::$defaultMacros['/t'] = '\'); ?>';


$application->run();


// translation helper
function t($message) {
    static $translator;
    if (!$translator) $translator = Environment::getService('Nette\ITranslator');
    
    $args = func_get_args();
    return call_user_func_array(callback($translator, 'translate'), $args);
}

// template translation macro {t}message{/t}
function tt($message) {
    /// parse message
    
    return t($message);
}

