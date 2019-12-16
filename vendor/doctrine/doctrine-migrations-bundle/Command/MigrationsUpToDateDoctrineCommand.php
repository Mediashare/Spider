<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MigrationsBundle\Command;

use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function assert;

/**
 * Command for checking if your database is up to date or not.
 */
class MigrationsUpToDateDoctrineCommand extends UpToDateCommand
{
    /** @var string */
    protected static $defaultName = 'doctrine:migrations:up-to-date';

    protected function configure() : void
    {
        parent::configure();

        $this
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'The database connection to use for this command.')
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')
            ->addOption('shard', null, InputOption::VALUE_REQUIRED, 'The shard connection to use for this command.');
    }

    public function initialize(InputInterface $input, OutputInterface $output) : void
    {
        /** @var Application $application */
        $application = $this->getApplication();

        Helper\DoctrineCommandHelper::setApplicationHelper($application, $input);

        $configuration = $this->getMigrationConfiguration($input, $output);
        $container     = $application->getKernel()->getContainer();
        assert($container instanceof ContainerInterface);
        DoctrineCommand::configureMigrations($container, $configuration);

        parent::initialize($input, $output);
    }

    public function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        // EM and DB options cannot be set at same time
        if ($input->getOption('em') !== null && $input->getOption('db') !== null) {
            throw new InvalidArgumentException('Cannot set both "em" and "db" for command execution.');
        }

        return parent::execute($input, $output);
    }
}
