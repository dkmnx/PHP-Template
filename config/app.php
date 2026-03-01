<?php

return [
    'name'     => getenv('APP_NAME') ?: 'App',
    'env'      => getenv('APP_ENV') ?: 'production',
    'base_url' => getenv('APP_BASE_URL') ?: '',
];
