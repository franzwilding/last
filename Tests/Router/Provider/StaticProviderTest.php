<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 27.08.18
 * Time: 11:29
 */

namespace Fw\LastBundle\Tests\Router\Provider;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class StaticProviderTest extends KernelTestCase
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

    public function testServiceEnabledConfig() {

        // Clear cache.
        static::bootKernel();
        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Default is true.
        static::bootKernel(['ignore_conditional_packages' => true]);
        $staticRouteProvider = self::$container->get('Fw\LastBundle\Router\Provider\StaticProvider');
        $accessDist = new \ReflectionProperty($staticRouteProvider, 'enabled');
        $accessDist->setAccessible(true);
        $this->assertEquals(true, $accessDist->getValue($staticRouteProvider));

        // Clear cache.
        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Config is false.
        static::bootKernel();
        $staticRouteProvider = self::$container->get('Fw\LastBundle\Router\Provider\StaticProvider');
        $accessDist = new \ReflectionProperty($staticRouteProvider, 'enabled');
        $accessDist->setAccessible(true);
        $this->assertEquals(false, $accessDist->getValue($staticRouteProvider));
    }

    public function testServiceReturnsRequests() {

        $expectedRequests = [
          Request::create('/'),
            Request::create('test_page_1'),
            Request::create('test_page_2.html', Request::METHOD_POST),
            Request::create('subdir/any/foo'),
            Request::create('foo.json'),
          Request::create('default'),
        ];

        // Clear cache.
        static::bootKernel();
        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());
        static::bootKernel();

        $staticRouteProvider = self::$container->get('Fw\LastBundle\Router\Provider\StaticProvider');
        $this->assertCount(6, $staticRouteProvider->getRoutes());
        foreach($staticRouteProvider->getRoutes() as $delta => $request) {
            $this->assertEquals($expectedRequests[$delta]->getUri(), $request->getUri());
            $this->assertEquals($expectedRequests[$delta]->getMethod(), $request->getMethod());
        }
    }
}