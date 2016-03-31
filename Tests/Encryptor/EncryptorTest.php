<?php

namespace Nmure\EncryptorBundle\Tests\Encryptor;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Nmure\EncryptorBundle\Encryptor\Encryptor;

class EncryptorTest extends TestCase
{
    private $secret = 'thisIsMySecretTestingKey';
    private $data = 'Lorem ipsum dolor';

    public function testDefaultEncryptDecrypt()
    {
        $encryptor = new Encryptor($this->secret);
        $this->assertEquals($this->data, $encryptor->decrypt($encryptor->encrypt($this->data)));
    }

    public function testEncryptDecryptWithIvSetting()
    {
        // encryptor with randomly generated IV
        $encryptor = new Encryptor($this->secret);
        $encrypted = $encryptor->encrypt($this->data);
        $iv = $encryptor->getIv();

        // creating a new encryptor to read encrypted data with the stored IV
        $encryptor = new Encryptor($this->secret);
        $encryptor->setIv($iv);
        $this->assertEquals($this->data, $encryptor->decrypt($encrypted));
    }

    public function testIvIsRamdomlyGenerated()
    {
        $encryptor1 = new Encryptor($this->secret);
        $encryptor2 = new Encryptor($this->secret);
        $this->assertNotEquals($encryptor1->getIv(), $encryptor2->getIv());
    }
}
