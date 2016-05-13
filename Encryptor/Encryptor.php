<?php

namespace Nmure\EncryptorBundle\Encryptor;

class Encryptor implements EncryptorInterface
{
    /**
     * The encryption key.
     * @var string
     */
    private $secret;

    /**
     * The cipher method.
     * @var string
     */
    private $cipher;

    /**
     * Initialization Vector.
     * @var string
     */
    private $iv;

    /**
     * Constructor.
     * 
     * @param string $secret The encryption key.
     * @param string $cipher The cipher method
     * @param int    $ivLength The length of Initialization Vector, in number of bytes.
     */
    public function __construct($secret, $cipher, $ivLength)
    {
        $this->secret = $secret;
        $this->cipher = $cipher;
        $this->iv = openssl_random_pseudo_bytes($ivLength);
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, $this->cipher, $this->secret, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($encrypted)
    {
        return openssl_decrypt($encrypted, $this->cipher, $this->secret, OPENSSL_RAW_DATA, $this->iv);
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
