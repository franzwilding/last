<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 09:34
 */

namespace Fw\LastBundle\Router;

use Symfony\Component\HttpFoundation\Request;

class RouteManager
{

    /**
     * @var RouteProvider[] $providers
     */
    private $providers;

    /**
     * @var Request[] $routes
     */
    private $routes;

    /**
     * @var boolean $requestStackConstructed
     */
    private $routesBuilt;

    public function __construct()
    {
        $this->providers = [];
        $this->routes = [];
        $this->routesBuilt = true;
    }

    /**
     * Register an route provider. It will be called when generating requestStack.
     *
     * @param RouteProvider $provider
     */
    public function registerRouteProvider(RouteProvider $provider) : void {
        if(!in_array($provider, $this->providers)) {
            $this->providers[] = $provider;

            // Reset routes cache.
            $this->routesBuilt = false;
        }
    }

    /**
     * Returns an array of requests that will match routes.
     *
     * @return Request[]
     */
    public function getRoutes() : array {
        if(!$this->routesBuilt) {
            $this->routesBuilt = true;

            foreach($this->providers as $provider) {
                $this->routes = array_merge($this->routes, $provider->getRoutes());
            }
        }

        return $this->routes;
    }
}