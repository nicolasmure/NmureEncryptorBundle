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
    public function testSecretMustNotBeEmpty()
    {
        $loader = new NmureEncryptorExtension();
        $config = $this->getEmptySecretConfig();
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSecretMustBeDefined()
    {
        $loader = new NmureEncryptorExtension();
        $config = $this->getEmptySecretConfig();
        unset($config['secret']);
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testValidConfiguration()
    {
        $configuration = new ContainerBuilder();
        $loader = new NmureEncryptorExtension();
        $config = $this->getValidConfig();
        $loader->load(array($config), $configuration);

        $this->assertEquals($configuration->getParameter('nmure_encryptor.secret'), 'iAmTheSecretKey');
        $this->assertInstanceOf('Nmure\EncryptorBundle\Encryptor\Encryptor', $configuration->get('nmure_encryptor.encryptor'));
    }

    private function getEmptySecretConfig()
    {
        $yaml = <<<EOF
secret: 
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    private function getValidConfig()
    {
        $yaml = <<<EOF
secret: iAmTheSecretKey
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }
}
