<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeToStringTransformer implements DataTransformerInterface
{
    public function transform($dateTime)
    {
        if (null === $dateTime) {
            return '';
        }

        return $dateTime->format('d-m-Y H:i');
    }

    public function reverseTransform($dateTimeString)
    {
        if (!$dateTimeString) {
            return null;
        }

        $dateTime = \DateTime::createFromFormat('d-m-Y H:i', $dateTimeString);

        if (false === $dateTime) {
            throw new TransformationFailedException(sprintf(
                'La date "%s" n\'est pas valide !',
                $dateTimeString
            ));
        }

        return $dateTime;
    }
}

