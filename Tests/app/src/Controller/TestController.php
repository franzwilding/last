<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 15:27
 */

namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function testPage1() {
        return $this->render('/test_page_1.html.twig');
    }

    public function testPage2() {
        return $this->redirectToRoute('test_page_1');
    }

    public function testPageJson() {
        return new Response('{ "foo": "baa" }');
    }
}