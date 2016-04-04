<?php

namespace Nmure\EncryptorBundle\Adapter;

class Base64Adapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function adapt($data)
    {
        return base64_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function revert($adapted)
    {
        return base64_decode($adapted);
    }
}
