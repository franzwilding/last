<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 28.08.18
 * Time: 14:30
 */

namespace Fw\LastBundle\Tests\Command;

use Fw\LastBundle\Command\DumpCommand;
use Fw\LastBundle\Router\RouteManager;
use Fw\LastBundle\Service\SiteGenerator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class DumpCommandTest extends KernelTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = array())
    {
        $kernel = parent::createKernel($options);
        $kernel->ignore_conditional_packages = isset($options['ignore_conditional_packages']) ? $options['ignore_conditional_packages'] : false;
        return $kernel;
    }

    public function testDistFolderDefaultConfiguration() {

        static::bootKernel();
        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Default is '%kernel.project_dir%/dist'.
        $testKernel = static::bootKernel(['ignore_conditional_packages' => true]);
        $command = self::$container->get('Fw\LastBundle\Command\DumpCommand');
        $accessDist = new \ReflectionProperty($command, 'defaultDistFolder');
        $accessDist->setAccessible(true);
        $this->assertEquals($testKernel->getContainer()->getParameter('kernel.project_dir') . '/dist', $accessDist->getValue($command));

        // Clear cache.
        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Config is'%kernel.project_dir%/var/dist'.
        $testKernel = static::bootKernel();
        $command = self::$container->get('Fw\LastBundle\Command\DumpCommand');
        $accessDist = new \ReflectionProperty($command, 'defaultDistFolder');
        $accessDist->setAccessible(true);
        $this->assertEquals($testKernel->getContainer()->getParameter('kernel.project_dir') . '/var/dist', $accessDist->getValue($command));
    }

    public function testExecute() {

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $defaultTestDist = $kernel->getContainer()->getParameter('kernel.project_dir') . '/baa';
        $testDist = $kernel->getContainer()->getParameter('kernel.project_dir') . '/foo';
        $testRequests = [
          Request::create('foo'),
          Request::create('baa'),
        ];

        $routeManagerMock = $this->getMockBuilder(RouteManager::class)->getMock();
        $routeManagerMock->method('getRoutes')->willReturn($testRequests);

        $fileSystemMock = $this->getMockBuilder(Filesystem::class)->getMock();
        $fileSystemMock->method('exists')->willReturnCallback(function($dist){
            return $dist == 'existing';
        });

        $siteGeneratorMock = $this->createMock(SiteGenerator::class, ['generate']);

        $application->add(new DumpCommand($routeManagerMock, $siteGeneratorMock, $fileSystemMock, $defaultTestDist));

        $command = $application->find('last:dump');

        $commandTester = new CommandTester($command);

        // Test that ->generate() was called with correct arguments.
        $siteGeneratorMock->expects($this->exactly(3))->method('generate')->withConsecutive(
          [$this->equalTo($testRequests), $this->equalTo($defaultTestDist)],
          [$this->equalTo($testRequests), $this->equalTo($testDist)]
        );

        // Test execute command without dist argument.
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertContains('Start dumping 2 responses as static files to '.$defaultTestDist.' folder.', $commandTester->getDisplay());
        $this->assertContains('Finished dumping.', $commandTester->getDisplay());

        // Test execute command with dist argument.
        $commandTester->execute(['command' => $command->getName(), '--dist' => $testDist]);
        $this->assertContains('Start dumping 2 responses as static files to '.$testDist.' folder.', $commandTester->getDisplay());
        $this->assertContains('Finished dumping.', $commandTester->getDisplay());

        // Test execute command when dist folder exists but do not allow to override.
        $commandTester->setInputs(['N']);
        $commandTester->execute(['command' => $command->getName(), '--dist' => 'existing']);
        $this->assertContains('Warning! Folder "existing" exists!', $commandTester->getDisplay());
        $this->assertContains('Do you really want to override it? All existing files will be deleted.', $commandTester->getDisplay());
        $this->assertContains('Override? [yes|NO]', $commandTester->getDisplay());
        $this->assertNotContains('Start dumping 2 responses as static files to existing folder.', $commandTester->getDisplay());
        $this->assertNotContains('Finished dumping.', $commandTester->getDisplay());

        // Test execute command when dist folder exists but allow to override.
        $commandTester->setInputs(['Y']);
        $commandTester->execute(['command' => $command->getName(), '--dist' => 'existing']);
        $this->assertContains('Warning! Folder "existing" exists!', $commandTester->getDisplay());
        $this->assertContains('Do you really want to override it? All existing files will be deleted.', $commandTester->getDisplay());
        $this->assertContains('Override? [yes|NO]', $commandTester->getDisplay());
        $this->assertContains('Start dumping 2 responses as static files to existing folder.', $commandTester->getDisplay());
        $this->assertContains('Finished dumping.', $commandTester->getDisplay());
    }
}