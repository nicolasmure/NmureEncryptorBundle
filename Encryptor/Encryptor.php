<?php

namespace Nmure\EncryptorBundle\Encryptor;

class Encryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * Initialization Vector.
     * @var string
     */
    private $iv;

    /**
     * Constructor.
     * 
     * @param string $secret The encryption key.
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
        $this->iv = openssl_random_pseudo_bytes(16);
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, 'AES-256-CBC', $this->secret, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($encrypted)
    {
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->secret, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * {@inheritdoc}
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * {@inheritdoc}
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }
}
