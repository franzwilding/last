<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 13:36
 */

namespace Fw\LastBundle\Service;

use Fw\LastBundle\Exception\RouteHandlingException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SiteGenerator
{

    /**
     * @var HttpKernelInterface $kernel
     */
    private $kernel;

    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Visits all routes
     *
     * @param \Symfony\Component\HttpFoundation\RequestStack $requests
     *
     * @throws \Fw\LastBundle\Exception\RouteHandlingException
     */
    public function generate(RequestStack $requests) {
        while ($request = $requests->pop()) {
            try {
                $response = $this->kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

                // TODO: Save content to html file.
                var_dump($response->getContent());

            } catch (\Exception $e) {
                throw new RouteHandlingException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }
    }
}