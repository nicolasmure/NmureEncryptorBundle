<?php

namespace Nmure\EncryptorBundle\Encryptor;

interface EncryptorInterface
{
    /**
     * @param  string $data Data to encrypt.
     * 
     * @return string       Encrypted data.
     */
    public function encrypt($data);

    /**
     * @param  string $encrypted Encypted data.
     * 
     * @return string            Decrypted data.
     */
    public function decrypt($encrypted);

    /**
     * @return string The Initialization Vector.
     */
    public function getIv();

    /**
     * @param string $iv The Initialization Vector to set.
     */
    public function setIv($iv);
}
