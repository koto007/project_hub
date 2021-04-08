<?php

namespace App\Serializer;

class CirculatorReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}