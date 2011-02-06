<?php

define('LIBS_DIR', WWW_DIR . '/app/libs');
define('TEMP_DIR', WWW_DIR . '/app/temp');
define('LOG_DIR',  WWW_DIR . '/app/temp/logs');
define('LANG_DIR', WWW_DIR . '/app/lang');

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


Mantra\TranslationHelper::register();


$application->run();




