<?php

namespace Nmure\EncryptorBundle\Tests\Adapter;

use Nmure\EncryptorBundle\Encryptor\Encryptor;
use Nmure\EncryptorBundle\Adapter\Base64Adapter;

class Base64AdapterTest extends AbstractAdapterTestHelper
{
    public function testAdapt()
    {
        $adapter = $this->getConcreteEncryptor();
        $encryptor = $this->encryptor;

        $this->assertSame(base64_encode($encryptor->encrypt($this->data)), $adapter->encrypt($this->data));
    }

    public function testRevert()
    {
        $adapter = $this->getConcreteEncryptor();
        $encryptor = $this->encryptor;

        $encrypted = $adapter->encrypt($this->data);
        $this->assertSame($encryptor->decrypt(base64_decode($encrypted)), $adapter->decrypt($encrypted));
    }

    public function testGetSetIv()
    {
        $adapter = $this->getConcreteEncryptor();
        $encryptor = $this->encryptor;

        $adaptedIv = base64_encode($this->iv);
        $adapter->setIv($adaptedIv);

        $this->assertSame($this->iv, $encryptor->getIv());
        $this->assertSame($this->iv, base64_decode($adapter->getIv()));
        $this->assertSame($adaptedIv, $adapter->getIv());
    }

    /**
     * {@inheritdoc}
     */
    protected function getConcreteEncryptor()
    {
        $this->encryptor = new Encryptor($this->secret);
        return new Base64Adapter($this->encryptor);
    }
}
