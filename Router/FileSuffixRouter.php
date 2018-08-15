<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 13.08.18
 * Time: 17:09
 */

namespace Fw\LastBundle\Router;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class FileSuffixRouter extends Router
{
    private $fileSuffixUrlGenerator = null;

    /**
     * {@inheritdoc}
     */
    public function getGenerator()
    {
        if(!$this->getContext()->getParameter('_fw_last')) {
            return parent::getGenerator();
        }

        if(!$this->fileSuffixUrlGenerator) {
            $this->fileSuffixUrlGenerator = new FileSuffixUrlGenerator($this->getRouteCollection(), $this->context, $this->logger);
        }

        return $this->fileSuffixUrlGenerator;
    }
}