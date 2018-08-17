<?php
/**
 * Created by PhpStorm.
 * User: franzwilding
 * Date: 17.08.18
 * Time: 09:30
 */

namespace Fw\LastBundle\Tests\Router;

use Fw\LastBundle\Router\FileSuffixUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileSuffixRouterTest extends KernelTestCase
{

    public function testSelectingUrlGenerator() {

        static::bootKernel([]);

        // Do not set _fw_last context variable.
        $this->assertNotInstanceOf(FileSuffixUrlGenerator::class, static::$kernel->getContainer()->get('router')->getGenerator());

        // Set _fw_last context ot false.
        static::$kernel->getContainer()->get('router')->getContext()->setParameter('_fw_last', false);
        $this->assertNotInstanceOf(FileSuffixUrlGenerator::class, static::$kernel->getContainer()->get('router')->getGenerator());

        // Set _fw_last context ot true.
        static::$kernel->getContainer()->get('router')->getContext()->setParameter('_fw_last', true);
        $this->assertInstanceOf(FileSuffixUrlGenerator::class, static::$kernel->getContainer()->get('router')->getGenerator());
    }

}