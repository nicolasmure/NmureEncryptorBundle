<?php

namespace Nmure\EncryptorBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Nmure\EncryptorBundle\DependencyInjection\NmureEncryptorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nmure\Encryptor\Encryptor;
use Nmure\Encryptor\Formatter\Base64Formatter;
use Nmure\Encryptor\Formatter\HexFormatter;

class NmureEncryptorExtensionTest extends TestCase
{
    private $secret = '452F93C1A737722D8B4ED8DD58766D99';
    private $secondSecret = '6A4E723D3F4AA81ACF776DCF2B6AEC45';
    private $cipher = 'AES-256-CBC';
    private $data = 'Lorem ipsum dolor';
    private $loader;
    private $configuration;

    protected function setUp()
    {
        $this->loader = new NmureEncryptorExtension();
        $this->configuration = new ContainerBuilder();
    }

    protected function tearDown()
    {
        unset($this->loader);
        unset($this->configuration);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEncryptorsArrayMustBeDefined()
    {
        $this->loader->load(array(array()), $this->configuration);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEncryptorsArrayMustNotBeEmpty()
    {
        $config = array(
            'encryptors' => array(),
        );
        $this->loader->load(array($config), $this->configuration);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSecretMustNotBeEmpty()
    {
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => null
                ),
            ),
        );
        $this->loader->load(array($config), $this->configuration);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSecretMustBeDefined()
    {
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(),
            ),
        );
        $this->loader->load(array($config), $this->configuration);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage cipher method is not supported
     */
    public function testUnsupportedCipherMethod()
    {
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                    'cipher' => 'unsupportedCipher',
                ),
            ),
        );
        $this->loader->load(array($config), $this->configuration);
    }

    public function testValidConfiguration()
    {
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                ),
            ),
        );
        $this->loader->load(array($config), $this->configuration);
        $this->configuration->compile();

        $this->assertInstanceOf('Nmure\Encryptor\Encryptor', $this->configuration->get('nmure_encryptor.first_encryptor'));
        // default settings
        $this->assertInstanceOf('Nmure\Encryptor\Formatter\Base64Formatter', $this->configuration->get('nmure_encryptor.formatters.base64_formatter'));
        $this->assertInstanceOf('Nmure\Encryptor\Formatter\HexFormatter', $this->configuration->get('nmure_encryptor.formatters.hex_formatter'));
    }

    public function testTurnHexKeyToBin()
    {
        $config = array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                    'turn_hex_key_to_bin' => true,
                ),
            ),
        );
        $this->loader->load(array($config), $this->configuration);
        $this->configuration->compile();

        $first = $this->configuration->get('nmure_encryptor.first_encryptor');
        $second = new Encryptor($this->secret, $this->cipher);

        $second->turnHexKeyToBin();
        $first->disableAutoIvUpdate();
        $second->disableAutoIvUpdate();
        $first->setIv($second->generateIv());

        $this->assertEquals($first->getIv(), $second->getIv());
        $this->assertEquals($first->encrypt($this->data), $second->encrypt($this->data));
    }

    public function testDisableAutoIVUpdate()
    {
        list($first, $second) = $this->getEncryptors(array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                ),
                'second_encryptor' => array(
                    'secret' => $this->secondSecret,
                    'disable_auto_iv_update' => true,
                ),
            ),
        ));

        // auto IV update should be enabled by default
        $enc11 = $first->encrypt($this->data);
        $iv11 = $first->getIv();
        $enc12 = $first->encrypt($this->data);
        $iv12 = $first->getIv();
        $this->assertNotEquals($enc11, $enc12);
        $this->assertNotEquals($iv11, $iv12);

        // we disabled the auto IV update in the config
        // for this encryptor
        $enc21 = $second->encrypt($this->data);
        $iv21 = $second->getIv();
        $enc22 = $second->encrypt($this->data);
        $iv22 = $second->getIv();
        $this->assertEquals($enc21, $enc22);
        $this->assertEquals($iv21, $iv22);
    }

    public function testFormattersUsage()
    {
        list($first, $second) = $this->getEncryptors(array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                    'formatter' => 'nmure_encryptor.formatters.base64_formatter',
                ),
                'second_encryptor' => array(
                    'secret' => $this->secondSecret,
                    'turn_hex_key_to_bin' => true,
                    'formatter' => 'nmure_encryptor.formatters.hex_formatter',
                ),
            ),
        ));

        $first2 = new Encryptor($this->secret, $this->cipher);
        $first2->setFormatter(new Base64Formatter());
        $first->disableAutoIvUpdate();
        $first2->disableAutoIvUpdate();
        $first->setIv($first2->generateIv());
        $this->assertEquals($first->encrypt($this->data), $first2->encrypt($this->data));

        $second2 = new Encryptor($this->secondSecret, $this->cipher);
        $second2->turnHexKeyToBin();
        $second2->setFormatter(new HexFormatter());
        $second->disableAutoIvUpdate();
        $second2->disableAutoIvUpdate();
        $second->setIv($second2->generateIv());
        $this->assertEquals($second->encrypt($this->data), $second2->encrypt($this->data));
    }

    public function testMultipleEncryptorsDeclaration()
    {
        list($first, $second) = $this->getEncryptors(array(
            'encryptors' => array(
                'first_encryptor' => array(
                    'secret' => $this->secret,
                ),
                'second_encryptor' => array(
                    'secret' => $this->secondSecret,
                ),
            ),
        ));

        // assert two encryptor instances of the same class
        $this->assertNotEquals(spl_object_hash($first), spl_object_hash($second));
        $this->assertEquals(get_class($first), get_class($second));

        // assert secret key is different
        $first->disableAutoIvUpdate();
        $second->disableAutoIvUpdate();
        $first->setIv($second->generateIv());
        $this->assertEquals($first->getIv(), $second->getIv());
        $this->assertNotEquals($first->encrypt($this->data), $second->encrypt($this->data));
    }

    private function getEncryptors(array $config)
    {
        $this->loader->load(array($config), $this->configuration);

        return array(
            $this->configuration->get('nmure_encryptor.first_encryptor'),
            $this->configuration->get('nmure_encryptor.second_encryptor'),
        );
    }
}
