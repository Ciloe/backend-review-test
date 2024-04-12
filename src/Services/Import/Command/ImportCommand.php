<?php

namespace App\Services\Import\Command;

use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand
{
    public function __construct(private SymfonyStyle $style, private $handle)
    {
    }

    public function getStyle(): SymfonyStyle
    {
        return $this->style;
    }

    public function getHandle()
    {
        return $this->handle;
    }
}
