<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RepoInsertInput
{
    public function __construct(
        private int $id,
        #[Assert\Length(max: 255)]
        private string $name,
        #[Assert\Length(max: 255)]
        private string $url,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
