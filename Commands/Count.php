<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\LoginLdap\Commands;

use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\LoginLdap\Ldap\ServerInfo;
use Piwik\Plugins\LoginLdap\Model\LdapUsers;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Piwik\Plugins\LoginLdap\Ldap\Client as LdapClient;

/**
 * TODO
 */
class Count extends ConsoleCommand
{
    /**
     * @var LdapUsers
     */
    private $ldapUsers;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->ldapUsers = LdapUsers::makeConfigured();
    }

    protected function configure()
    {
        $this->setName('loginldap:count');
        $this->setDescription('Query the configured LDAP servers and print the count of results for debug purposes. Use -vvv to get debug log output.');
        $this->addArgument('filter', InputArgument::REQUIRED, "The LDAP filter to search for.");
        $this->addOption('basedn', null, InputOption::VALUE_REQUIRED, "The base DN to use. Defaults to the base DN supplied in current configuration.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        $baseDnOverride = $input->getOption('basedn');

        $output->writeln("Executing filter <comment>$filter</comment> for count...");

        $ldapUsers = $this->ldapUsers;
        $result = (int)$ldapUsers->doWithClient(function (LdapUsers $self, LdapClient $ldapClient, ServerInfo $server) use ($filter, $ldapUsers, $baseDnOverride) {
            $ldapUsers->bindAsAdmin($ldapClient, $server);
            $baseDn = $baseDnOverride ?: $server->getBaseDn();

            return $ldapClient->count($baseDn, $filter);
        });

        $output->writeln("Found <comment>$result</comment> results.");
    }
}
