<?php
require __DIR__ . '/../vendor/autoload.php';

//Register an autoloader
$loader = new \Phalcon\Loader();
$loader->registerDirs([
	__DIR__ . '/../app',
])->register();

$debugMode		 = FALSE;
$debugSwitchFile = __DIR__ . '/local/debug';

if (file_exists($debugSwitchFile)) {
	$debugMode = trim(mb_strtolower(file_get_contents($debugSwitchFile))) === 'true' ? TRUE : FALSE;
}

Tracy\Debugger::enable(!$debugMode, __DIR__ . '/../log');

$config = new \Phalcon\Config\Adapter\Ini(__DIR__ . '/config/config.ini');

$localConfigFile = __DIR__ . '/local/config.local.ini';

if (file_exists($localConfigFile)) {
	$localConfig = new \Phalcon\Config\Adapter\Ini($localConfigFile);
	$config->merge($localConfig);
}
//Create a DI
$di	 = \DI\DIFactory::create((object) $config->toArray());
//Handle the request
$app = new \Phalcon\Mvc\Micro($di);

$events = new \Phalcon\Mvc\Micro\Collection;

$events->setHandler($di->get('eventsController'));
$events->setPrefix('/events');
$events->post('/', 'create');
$events->get('/{type:[a-z]+}?{query}', 'show');

$app->mount($events);

$app->notFound(function () use ($app) {
	$app->response->setStatusCode(404, "Not Found")->sendHeaders();
	echo 'NOT FOUND BITCH';
});

return $app;
