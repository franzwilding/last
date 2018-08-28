<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 13:36
 */

namespace Fw\LastBundle\Service;

use Fw\LastBundle\Exception\RouteHandlingException;
use Fw\LastBundle\Router\FileSuffixUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SiteGenerator
{

    /**
     * @var HttpKernelInterface $kernel
     */
    private $kernel;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var Filesystem $fileSystem
     */
    private $fileSystem;

    public function __construct(HttpKernelInterface $kernel, Router $router, Filesystem $fileSystem)
    {
        $this->router = $router;
        $this->kernel = $kernel;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Visits all routes and save the content as html file to dist folder.
     *
     * @param array $requests
     * @param string $dist_folder
     *
     * @throws RouteHandlingException
     */
    public function generate(array $requests, string $dist_folder) : void {

        // Clear dist folder.
        $this->fileSystem->remove($dist_folder);
        $this->fileSystem->mkdir($dist_folder);

        foreach($requests as $request) {

            $this->router->getContext()->setParameter('_fw_last', true);

            try {
                $response = $this->kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
                $this->fileSystem->dumpFile(
                  $dist_folder.FileSuffixUrlGenerator::appendSuffix($request->getPathInfo()),
                    $response->getContent()
                );


            } catch (\Exception $e) {
                throw new RouteHandlingException($e->getMessage(), $e->getCode(), $e->getPrevious());
            } finally {
                $this->router->getContext()->setParameter('_fw_last', null);
            }
        }
    }
}