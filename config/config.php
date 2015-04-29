<?php

use Interop\Container\ContainerInterface;
use Piwik\Plugins\LoginLdap\Auth\LdapAuth;
use Piwik\Plugins\LoginLdap\Auth\SynchronizedAuth;

return array(
    'Piwik\Plugins\LoginLdap\Model\LdapUsers' => function (ContainerInterface $c) {
        return \Piwik\Plugins\LoginLdap\Model\LdapUsers::makeConfigured();
    },

    'Piwik\Plugins\LoginLdap\LdapInterop\UserSynchronizer' => function (ContainerInterface $c) {
        return \Piwik\Plugins\LoginLdap\LdapInterop\UserSynchronizer::makeConfigured();
    },

    'Piwik\Plugins\LoginLdap\Auth\WebServerAuth.fallbackAuth' => function (ContainerInterface $c) {
        if ($c->get('ini.LoginLdap.use_ldap_for_authentication') == 1) {
            return LdapAuth::makeConfigured();
        } else {
            return SynchronizedAuth::makeConfigured();
        }
    },

    'Piwik\Plugins\LoginLdap\Auth\WebServerAuth' => DI\object()
        ->constructorParameter('synchronizeUsersAfterSuccessfulLogin', DI\link('ini.LoginLdap.synchronize_users_after_login'))
        ->constructorParameter('fallbackAuth', DI\link('Piwik\Plugins\LoginLdap\Auth\WebServerAuth.fallbackAuth')),

    'Piwik\Auth' => function (ContainerInterface $c) {
        if ($c->get('ini.LoginLdap.use_webserver_auth') == 1) {
            return $c->get('Piwik\Plugins\LoginLdap\Auth\WebServerAuth');
        } else if ($c->get('ini.LoginLdap.use_ldap_for_authentication') == 1) {
            return LdapAuth::makeConfigured();
        } else {
            return SynchronizedAuth::makeConfigured();
        }
    }
);