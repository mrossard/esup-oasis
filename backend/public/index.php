<?php
//https://symfony.com/doc/current/setup/file_permissions.html#3-without-using-acl
umask(0000);

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
};
