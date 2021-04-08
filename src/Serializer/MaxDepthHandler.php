<?php

namespace App\Serializer;

class MaxDepthHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}