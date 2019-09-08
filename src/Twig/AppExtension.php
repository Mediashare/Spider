<?php 
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('is_array', [$this, 'isArray']),
        ];
    }

    public function isArray($variable) {
        return is_array($variable);
    }

}