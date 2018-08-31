<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 09:59
 */

namespace Fw\LastBundle\Tests\DependencyInjection;

use Fw\LastBundle\DependencyInjection\Compiler\RouteProviderPass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RouteProviderPassTest extends KernelTestCase
{
    public function testCompilerPass() {
        // Make sure, that the test provider was registered.
        static::bootKernel();
        $this->assertGreaterThan(2, count(static::$kernel->getContainer()->get('fw.last.route_manager')->getRoutes()));
        $this->assertEquals('/foo', static::$kernel->getContainer()->get('fw.last.route_manager')->getRoutes()[0]->getPathInfo());
        $this->assertEquals('/baa', static::$kernel->getContainer()->get('fw.last.route_manager')->getRoutes()[1]->getPathInfo());
    }

    public function testCompilerPassWithoutRegisteredService() {

        // Make sure, that compiler pass will not fail if service was not defined
        $compilerPass = new RouteProviderPass();
        $this->assertNull($compilerPass->process(new ContainerBuilder()));
    }
}