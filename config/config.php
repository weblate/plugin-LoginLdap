<?php

use Piwik\Plugins\LoginLdap\Auth\Base as AuthBase;
use Interop\Container\ContainerInterface;

return array(
    'Piwik\Auth' => function (ContainerInterface $c) {
        return AuthBase::factory();
    }
);