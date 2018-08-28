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
        $this->mocked_dist = 'foo';
        $this->mockedFileSystem = $this->createMock(Filesystem::class, ['remove', 'mkdir', 'dumpFile']);
        $this->siteGenerator = new SiteGenerator(
          static::$kernel,
          static::$kernel->getContainer()->get('router'),
          $this->mockedFileSystem
        );
    }

    /**
     * @expectedException \Fw\LastBundle\Exception\RouteHandlingException
     * @expectedExceptionMessage No route found for "GET /foo
     */
    public function testGeneratingSiteForInvalidRoute() {
        static::$kernel->getContainer()->get('fw_last.site_generator')->generate([Request::create('foo.html')], $this->mocked_dist);
    }

    public function testGenerateSiteForValidRoutes() {
        $this->mockedFileSystem->expects($this->once())->method('remove')->with($this->equalTo($this->mocked_dist));
        $this->mockedFileSystem->expects($this->once())->method('mkdir')->with($this->equalTo($this->mocked_dist));

        $this->mockedFileSystem->expects($this->exactly(4))->method('dumpFile')->withConsecutive(
          [$this->equalTo($this->mocked_dist.'/foo.json'), $this->equalTo('{ "foo": "baa" }')],
          [$this->equalTo($this->mocked_dist.'/subdir/any/foo.html'), $this->stringContains('<h1>Test Page 1</h1>')],
          [$this->equalTo($this->mocked_dist.'/test_page_2.html'), $this->stringContains('<meta http-equiv="refresh" content="0;url=/test_page_1.html" />')],
        [$this->equalTo($this->mocked_dist.'/test_page_1.html'), $this->stringContains('<h1>Test Page 1</h1>')]
        );

        $this->siteGenerator->generate([
          Request::create('foo.json'),
          Request::create('subdir/any/foo'),
          Request::create('test_page_2.html', Request::METHOD_POST),
          Request::create('test_page_1')
        ], $this->mocked_dist);
    }
}