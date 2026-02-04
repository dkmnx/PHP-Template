<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../app/helpers.php';

use App\Core\Router;

$router = new Router();

require __DIR__ . '/../routes/web.php';

$router->dispatch();
