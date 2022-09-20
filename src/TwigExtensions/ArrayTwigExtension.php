<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayTwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('average', [$this, 'classAverage'])
        ];
    }
    public function classAverage(array $notes)
    {
        return number_format(array_sum($notes)/count($notes), 2, ',', '.');
    }
}