<?php
$app = require __DIR__.'/../app/bootstrap.php';

$uri = filter_input(INPUT_SERVER, 'REQUEST_URI');

$res = $app->handle($uri);
