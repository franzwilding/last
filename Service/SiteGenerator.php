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
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @var string $dist_folder
     */
    private $dist_folder;

    public function __construct(HttpKernelInterface $kernel, Router $router, Filesystem $fileSystem, string $dist_folder)
    {
        $this->router = $router;
        $this->kernel = $kernel;
        $this->fileSystem = $fileSystem;
        $this->dist_folder = $dist_folder;
    }

    /**
     * Visits all routes and save the content as html file to dist folder.
     *
     * @param RequestStack $requestStack
     *
     * @throws RouteHandlingException
     */
    public function generate(RequestStack $requestStack) {

        // Clear dist folder.
        $this->fileSystem->remove($this->dist_folder);
        $this->fileSystem->mkdir($this->dist_folder);

        while ($request = $requestStack->pop()) {

            $this->router->getContext()->setParameter('_fw_last', true);

            try {
                $response = $this->kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
                $this->fileSystem->dumpFile(
                  $this->dist_folder.FileSuffixUrlGenerator::appendSuffix($request->getPathInfo()),
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