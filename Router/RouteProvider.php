<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 09:35
 */

namespace Fw\LastBundle\Router;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RouteProvider
 */
interface RouteProvider
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
    public function getRoutes() : array;
}