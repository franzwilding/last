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
use Symfony\Component\HttpFoundation\Request;

class RouteProviderPassTest extends KernelTestCase
{
    public function testCompilerPass() {
        // Make sure, that the test provider was registered.
        static::bootKernel();
        $this->assertEquals([
          Request::create('foo'),
          Request::create('baa'),
        ], static::$kernel->getContainer()->get('fw.last.route_manager')->getRoutes());
    }

    public function testCompilerPassWithoutRegisteredService() {

        // Make sure, that compiler pass will not fail if service was not defined
        $compilerPass = new RouteProviderPass();
        $this->assertNull($compilerPass->process(new ContainerBuilder()));
    }
}