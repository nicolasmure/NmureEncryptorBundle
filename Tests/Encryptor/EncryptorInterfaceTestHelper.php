<?php

namespace Nmure\EncryptorBundle\Tests\Encryptor;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

abstract class EncryptorInterfaceTestHelper extends TestCase
{
    protected $secret = 'thisIsMySecretTestingKey';
    protected $cipher = 'AES-256-CBC';
    protected $ivLength = 16;
    protected $data = 'Lorem ipsum dolor';

    public function testDefaultEncryptDecrypt()
    {
        $encryptor = $this->getConcreteEncryptor();
        $this->assertSame($this->data, $encryptor->decrypt($encryptor->encrypt($this->data)));
    }

    public function testEncryptDecryptWithIvSetting()
    {
        // encryptor with randomly generated IV
        $encryptor = $this->getConcreteEncryptor();
        $encrypted = $encryptor->encrypt($this->data);
        $iv = $encryptor->getIv();

        // creating a new encryptor to read encrypted data with the stored IV
        $encryptor = $this->getConcreteEncryptor();
        $encryptor->setIv($iv);
        $this->assertSame($this->data, $encryptor->decrypt($encrypted));
    }

    public function testIvIsRamdomlyGenerated()
    {
        $encryptor1 = $this->getConcreteEncryptor();
        $encryptor2 = $this->getConcreteEncryptor();
        $this->assertNotEquals($encryptor1->getIv(), $encryptor2->getIv());
    }

    /**
     * Returns the concrete Encryptor to test.
     * 
     * @return Nmure\EncryptorBundle\Encryptor\EncryptorInterface
     */
    abstract protected function getConcreteEncryptor();
}
