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
class Query extends ConsoleCommand
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
        $this->setName('loginldap:query');
        $this->setDescription('Query the configured LDAP servers for debug purposes. Use -vvv to get debug log output.');
        $this->addArgument('filter', InputArgument::REQUIRED, "The LDAP filter to search for.");
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, "The number of results to print out. Some queries can "
            . "return a lot of data, for debugging it is a good idea to use a limit.");
        $this->addOption('basedn', null, InputOption::VALUE_REQUIRED, "The base DN to use. Defaults to the base DN supplied in current configuration.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        $limit = $input->getOption('limit');
        $baseDnOverride = $input->getOption('basedn');

        $output->writeln("Executing filter <comment>$filter</comment>...");

        $ldapUsers = $this->ldapUsers;
        $result = $ldapUsers->doWithClient(function (LdapUsers $self, LdapClient $ldapClient, ServerInfo $server) use ($filter, $ldapUsers, $baseDnOverride) {
            $ldapUsers->bindAsAdmin($ldapClient, $server);
            $baseDn = $baseDnOverride ?: $server->getBaseDn();

            return $ldapClient->fetchAll($baseDn, $filter);
        });

        if ($limit) {
            $result = array_slice($result, 0, $limit);
        }

        $output->writeln("Results:");
        $output->writeln("<info>".var_export($result, true)."</info>");
    }
}
