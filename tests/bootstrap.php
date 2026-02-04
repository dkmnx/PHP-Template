<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Test database configuration
putenv('DB_HOST=localhost');
putenv('DB_NAME=test_db');
putenv('DB_USER=test_user');
putenv('DB_PASS=test_pass');
