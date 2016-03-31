<?php

namespace Nmure\EncryptorBundle\Encryptor;

class Encryptor
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
     * @param  string $data Data to encrypt.
     * @return string       Encrypted data.
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, 'AES-256-CBC', $this->secret, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * @param  string $crypted Encypted data.
     * @return string          Decrypted data.
     */
    public function decrypt($crypted)
    {
        return openssl_decrypt($crypted, 'AES-256-CBC', $this->secret, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * @return string The Initialization Vector.
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param string $iv The Initialization Vector to set.
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }
}
