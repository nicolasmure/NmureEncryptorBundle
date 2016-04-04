<?php

namespace Nmure\EncryptorBundle\Adapter;

use Nmure\EncryptorBundle\Encryptor\EncryptorInterface;

abstract class AbstractAdapter implements EncryptorInterface
{
    /**
     * Adapted EncryptorInterface
     * 
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Constructor.
     * 
     * @param EncryptorInterface $encryptor The EncryptorInterface to adapt.
     */
    public function __construct(EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        $encrypted = $this->encryptor->encrypt($data);
        return $this->adapt($encrypted);
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($encrypted)
    {
        $reverted = $this->revert($encrypted);
        return $this->encryptor->decrypt($reverted);
    }

    /**
     * {@inheritdoc}
     */
    public function getIv()
    {
        $iv = $this->encryptor->getIv();
        return $this->adapt($iv);
    }

    /**
     * {@inheritdoc}
     */
    public function setIv($iv)
    {
        $reverted = $this->revert($iv);
        $this->encryptor->setIv($reverted);
    }

    /**
     * Adapts the given data to a format this
     * adapter can handle.
     * 
     * @param  string $data
     * 
     * @return string
     */
    abstract protected function adapt($data);

    /**
     * Revert the given adapted data to a format
     * the adapted encryptor can handle.
     * 
     * @param  string $adapted
     * 
     * @return string
     */
    abstract protected function revert($adapted);
}
