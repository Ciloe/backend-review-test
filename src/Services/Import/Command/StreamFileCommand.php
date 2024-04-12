<?php

namespace App\Services\Import\Command;

class StreamFileCommand
{
    public function __construct(private string $file)
    {
    }

    public function getFile(): string
    {
        return $this->file;
    }
}
