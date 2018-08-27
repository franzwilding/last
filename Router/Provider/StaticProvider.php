<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 27.08.18
 * Time: 11:24
 */

namespace Fw\LastBundle\Router\Provider;

use Fw\LastBundle\Router\RouteProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Router;

/**
 * Provides all configured routes of the application without placeholders.
 *
 * @package Fw\LastBundle\Router\Provider
 */
class StaticProvider implements RouteProvider
{

    /**
     * @var boolean $enabled
     */
    private $enabled;

    /**
     * @var Router $router
     */
    private $router;

    public function __construct(bool $enabled, Router $router)
    {
        $this->enabled = $enabled;
        $this->router = $router;
    }

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
        $requests = [];

        foreach($this->router->getRouteCollection() as $route_name => $route) {

            $method = 'GET';

            if(!empty($route->getMethods()) && !in_array($method, $route->getMethods())) {
                $method = $route->getMethods()[0];
            }

            try {
                $requests[] = Request::create($this->router->generate($route_name), $method);
            } catch (MissingMandatoryParametersException $exception) {
                // We only want to generate routes without dynamic placeholders, so we can just ignore this.
            }
        }

        return $requests;
    }
}