<?php

namespace Nmure\EncryptorBundle\Tests\Adapter;

use Nmure\EncryptorBundle\Tests\Encryptor\EncryptorInterfaceTestHelper;

abstract class AbstractAdapterTestHelper extends EncryptorInterfaceTestHelper
{
    /**
     * Adapted EncryptorInterface
     * 
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var string
     */
    protected $iv = 'thisIsTheTestingIv';

    abstract public function testAdapt();

    abstract public function testRevert();

    abstract public function testGetSetIv();
}
