<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 28.08.18
 * Time: 14:25
 */

namespace Fw\LastBundle\Command;

use Fw\LastBundle\Router\RouteManager;
use Fw\LastBundle\Service\SiteGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{

    /**
     * @var string $defaultDist
     */
    private $defaultDistFolder;

    /**
     * @var RouteManager $routeManager
     */
    private $routeManager;

    /**
     * @var SiteGenerator $siteGenerator
     */
    private $siteGenerator;

    public function __construct(RouteManager $routeManager, SiteGenerator $siteGenerator, string $defaultDistFolder)
    {
        $this->defaultDistFolder = $defaultDistFolder;
        $this->routeManager = $routeManager;
        $this->siteGenerator = $siteGenerator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('last:dump')
            ->setDescription('Dumps static files to dist folder for all defined requests.')
            ->setHelp('This command runs all defined requests against your application and dumps the responses as static files to the dist folder.')
            ->addOption('dist', 'd', InputOption::VALUE_OPTIONAL, 'Set output dir. If left empty, the default one will be used. ATTENTION: All existing files at this location will be deleted!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dist_folder = $input->getOption('dist') ?? $this->defaultDistFolder;
        $routes = $this->routeManager->getRoutes();

        $output->writeln('Start dumping <info>'.count($routes).'</info> responses as static files to <info>'.$dist_folder.'</info> folder.');

        $this->siteGenerator->generate($routes, $dist_folder);

        $output->writeln('<info>Finished dumping.</info>');
    }
}