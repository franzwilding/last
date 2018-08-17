<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 09:46
 */

namespace Fw\LastBundle\Tests\Router;

use Fw\LastBundle\Router\RouteManager;
use Fw\LastBundle\Router\RouteProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteManagerTest extends TestCase
{
    public function testRegisterProviderAndReturn() {

        $manager = new RouteManager();
        $this->assertEmpty($manager->getRoutes());

        $manager->registerRouteProvider(new class implements RouteProvider {
            public function getRoutes(): array { return [Request::create('index'), Request::create('article/1')]; }
        });

        $manager->registerRouteProvider(new class implements RouteProvider {
            public function getRoutes(): array { return [Request::create('foo')]; }
        });

        $this->assertEquals([
          Request::create('index'),
          Request::create('article/1'),
          Request::create('foo')
        ], $manager->getRoutes());

    }
}