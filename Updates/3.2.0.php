<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\LoginLdap;

use Piwik\Updates;

/**
 */
class Updates_3_2_0 extends Updates
{
    static function update()
    {
        $config = \Piwik\Config::getInstance();

        // in version 3.2, the user_email_suffix config was split into user_email_suffix and user_login_suffix.
        // for existing installs, we should just initialize one w/ the other.
        if (isset($config->LoginLdap['user_email_suffix'])) {
            $config->LoginLdap['user_login_suffix'] = $config->LoginLdap['user_email_suffix'];
            $config->forceSave();
        }
    }
}
