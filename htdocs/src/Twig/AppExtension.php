<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('cast_to_array', [$this, 'castToArray'])
        ];
    }

    /**
     * Converts an object into an associative object
     *
     * @param object $object
     * @return array
     */
    public function castToArray(object $object): array
    {
        return (array) $object;
    }
    
}
