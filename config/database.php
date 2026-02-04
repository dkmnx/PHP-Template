<?php

declare(strict_types=1);

$config = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'name' => getenv('DB_NAME') ?: throw new RuntimeException('DB_NAME environment variable is required'),
    'user' => getenv('DB_USER') ?: throw new RuntimeException('DB_USER environment variable is required'),
    'pass' => getenv('DB_PASS') ?: '',
];

return $config;
