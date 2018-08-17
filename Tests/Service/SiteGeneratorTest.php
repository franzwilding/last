<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 14:01
 */

namespace Fw\LastBundle\Tests\Service;


use Fw\LastBundle\Service\SiteGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SiteGeneratorTest extends KernelTestCase
{

    private $mocked_dist = 'mocked_dist_folder';

    /**
     * @var Filesystem $mockedFileSystem
     */
    private $mockedFileSystem;

    /**
     * @var SiteGenerator $siteGenerator
     */
    private $siteGenerator;

    public function setUp() {
        static::bootKernel([]);
        $this->mockedFileSystem = $this->createMock(Filesystem::class, ['remove', 'mkdir', 'dumpFile']);
        $this->siteGenerator = new SiteGenerator(
          static::$kernel,
          static::$kernel->getContainer()->get('router'),
          $this->mockedFileSystem,
          $this->mocked_dist
        );
    }

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

        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Default is '%kernel.project_dir%/dist'
        $testKernel = static::bootKernel(['ignore_conditional_packages' => true]);
        $siteGenerator = $testKernel->getContainer()->get('fw_last.site_generator');
        $accessDist = new \ReflectionProperty($siteGenerator, 'dist_folder');
        $accessDist->setAccessible(true);
        $this->assertEquals($testKernel->getContainer()->getParameter('kernel.project_dir') . '/dist', $accessDist->getValue($siteGenerator));

        $fileSystem = new Filesystem();
        $fileSystem->remove(static::$kernel->getCacheDir());

        // Config is'%kernel.project_dir%/var/dist'
        $testKernel = static::bootKernel();
        $siteGenerator = $testKernel->getContainer()->get('fw_last.site_generator');
        $accessDist = new \ReflectionProperty($siteGenerator, 'dist_folder');
        $accessDist->setAccessible(true);
        $this->assertEquals($testKernel->getContainer()->getParameter('kernel.project_dir') . '/var/dist', $accessDist->getValue($siteGenerator));
    }

    /**
     * @expectedException \Fw\LastBundle\Exception\RouteHandlingException
     * @expectedExceptionMessage No route found for "GET /foo
     */
    public function testGeneratingSiteForInvalidRoute() {
        $requests = new RequestStack();
        $requests->push(Request::create('foo.html'));
        static::$kernel->getContainer()->get('fw_last.site_generator')->generate($requests);
    }

    public function testGenerateSiteForValidRoutes() {
        $requests = new RequestStack();
        $requests->push(Request::create('test_page_1'));
        $requests->push(Request::create('test_page_2.html', Request::METHOD_POST));
        $requests->push(Request::create('subdir/any/foo'));
        $requests->push(Request::create('foo.json'));

        $this->mockedFileSystem->expects($this->once())->method('remove')->with($this->equalTo($this->mocked_dist));
        $this->mockedFileSystem->expects($this->once())->method('mkdir')->with($this->equalTo($this->mocked_dist));

        $this->mockedFileSystem->expects($this->exactly(4))->method('dumpFile')->withConsecutive(
          [$this->equalTo($this->mocked_dist.'/foo.json'), $this->equalTo('{ "foo": "baa" }')],
          [$this->equalTo($this->mocked_dist.'/subdir/any/foo.html'), $this->stringContains('<h1>Test Page 1</h1>')],
          [$this->equalTo($this->mocked_dist.'/test_page_2.html'), $this->stringContains('<meta http-equiv="refresh" content="0;url=/test_page_1.html" />')],
        [$this->equalTo($this->mocked_dist.'/test_page_1.html'), $this->stringContains('<h1>Test Page 1</h1>')]
        );

        $this->siteGenerator->generate($requests);
    }
}