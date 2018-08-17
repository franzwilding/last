<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 10:00
 */

namespace Tests\App\RouteProvider;


use Fw\LastBundle\Router\RouteProvider;
use Symfony\Component\HttpFoundation\Request;

class TestRouteProvider implements RouteProvider
{

    /**
     * Return requests to match routes.
     *
     * Examples:
     *   return [
     *     Request::create('index');
     *     Request::create('article/1');
     *     Request::create('foo', Request::METHOD_POST);
     *   ];
     *
     * @return Request[]
     */
    public function getRoutes(): array
    {
        return [
          Request::create('foo'),
          Request::create('baa'),
        ];
    }
}