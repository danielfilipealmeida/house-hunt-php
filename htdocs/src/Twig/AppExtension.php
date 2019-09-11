<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('cast_to_array', [$this, 'castToArray']),
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
            new TwigFilter('geo_to_string', [$this, 'geoToString']),
        ];
    }

    /**
     * Converts an object into an associative object.
     *
     * @param object $object
     *
     * @return array
     */
    public function castToArray(object $object): array
    {
        return (array) $object;
    }

    /**
     * Converts an object that stores a encoded json into an array.
     *
     * @param object $string
     *
     * @return array
     */
    public function jsonDecode(string $string): array
    {
        return \json_decode($string, true);
    }

    /**
     * Converts an array containing geolocation coordinates to a printable string.
     * TODO: i18n, translatable constant strings
     *
     * @param array $geoLocation
     *
     * @return string
     */
    public function geoToString(array $geoLocation): string
    {
        return implode(
            ', ',
            [
                $geoLocation['latitude'] ?? 'unknown latitude',
                $geoLocation['longitude'] ?? 'unknown longitude'
            ]
        );
    }
}
