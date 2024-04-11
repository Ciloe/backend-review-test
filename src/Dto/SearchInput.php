<?php

namespace App\Dto;

use DateTimeImmutable;

class SearchInput
{
    public DateTimeImmutable $date;

    public string $keyword;

    public function isInitialized(): bool
    {
        $propertyDate = new \ReflectionProperty(SearchInput::class, 'date');
        $propertyKeyword = new \ReflectionProperty(SearchInput::class, 'keyword');

        return $propertyDate->isInitialized($this) && $propertyKeyword->isInitialized($this);
    }
}
