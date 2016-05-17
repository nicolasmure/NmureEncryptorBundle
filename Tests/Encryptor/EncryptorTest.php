<?php

namespace Nmure\EncryptorBundle\Tests\Encryptor;

use Nmure\EncryptorBundle\Encryptor\Encryptor;

class EncryptorTest extends EncryptorInterfaceTestHelper
{
    /**
     * {@inheritdoc}
     */
    protected function getConcreteEncryptor()
    {
        return new Encryptor($this->secret, $this->cipher);
    }
}
