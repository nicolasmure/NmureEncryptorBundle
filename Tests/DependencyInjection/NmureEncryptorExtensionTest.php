<?php

namespace Nmure\EncryptorBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Nmure\EncryptorBundle\DependencyInjection\NmureEncryptorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class NmureEncryptorExtensionTest extends TestCase
{
    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEncryptorsArrayMustBeDefined()
    {
        $loader = new NmureEncryptorExtension();
        $config = array();
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEncryptorsArrayMustNotBeEmpty()
    {
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(),
        );
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSecretMustNotBeEmpty()
    {
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => null
                ),
            ),
        );
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSecretMustBeDefined()
    {
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(),
            ),
        );
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage cipher method is not supported
     */
    public function testUnsupportedCipherMethod()
    {
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => 'iAmTheFirstSecretKey',
                    'cipher' => 'unsupportedCipher',
                ),
            ),
        );
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testValidConfiguration()
    {
        $configuration = new ContainerBuilder();
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => 'iAmTheFirstSecretKey',
                ),
            ),
        );
        $loader->load(array($config), $configuration);
        $configuration->compile();

        $this->assertInstanceOf('Nmure\EncryptorBundle\Encryptor\EncryptorInterface', $configuration->get('nmure_encryptor.first_encryptor'));
        // default setting
        $this->assertInstanceOf('Nmure\EncryptorBundle\Adapter\Base64Adapter', $configuration->get('nmure_encryptor.first_encryptor'));
    }

    public function testPreferOriginalEncryptor()
    {
        $configuration = new ContainerBuilder();
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => 'iAmTheFirstSecretKey',
                    'prefer_base64' => false,
                ),
            ),
        );
        $loader->load(array($config), $configuration);

        $this->assertInstanceOf('Nmure\EncryptorBundle\Encryptor\EncryptorInterface', $configuration->get('nmure_encryptor.first_encryptor'));
        $this->assertInstanceOf('Nmure\EncryptorBundle\Encryptor\Encryptor', $configuration->get('nmure_encryptor.first_encryptor'));
    }

    public function testMultipleEncryptorsDeclaration()
    {
        $configuration = new ContainerBuilder();
        $loader = new NmureEncryptorExtension();
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => 'iAmTheFirstSecretKey',
                ),
                'second_encryptor' => array(
                    'secret' => 'iAmTheSecondSecretKey',
                ),
            ),
        );
        $loader->load(array($config), $configuration);

        $first = $configuration->get('nmure_encryptor.first_encryptor');
        $second = $configuration->get('nmure_encryptor.second_encryptor');

        // assert two encryptor instances of the same class
        $this->assertFalse($first === $second);
        $this->assertTrue(get_class($first) === get_class($second));

        // assert secret key is different
        $data = 'Lorem ipsum dolor';
        $second->setIv($first->getIv());
        $this->assertNotEquals($first->encrypt($data), $second->encrypt($data));
    }
}
