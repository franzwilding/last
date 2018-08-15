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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SiteGeneratorTest extends KernelTestCase
{

    /**
     * @var SiteGenerator $siteGenerator
     */
    private $siteGenerator;

    public function setUp() {
        static::bootKernel([]);
        $this->siteGenerator = static::$kernel->getContainer()->get('fw_last.site_generator');
    }

    /**
     * @expectedException \Fw\LastBundle\Exception\RouteHandlingException
     * @expectedExceptionMessage No route found for "GET /foo
     */
    public function testGeneratingSiteForInvalidRoute() {
        $requests = new RequestStack();
        $requests->push(Request::create('foo.html'));
        $this->siteGenerator->generate($requests);
    }

    public function testGenerateSiteForValidRoutes() {
        $requests = new RequestStack();
        $requests->push(Request::create('test_page_1'));
        $requests->push(Request::create('test_page_2.html', Request::METHOD_POST));
        $requests->push(Request::create('subdir/any/foo'));
        $this->siteGenerator->generate($requests);
    }
}