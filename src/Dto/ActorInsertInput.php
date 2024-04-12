<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ActorInsertInput
{
    public function __construct(
        private int $id,
        #[Assert\Length(max: 255)]
        private string $login,
        #[Assert\Length(max: 255)]
        private string $url,
        #[Assert\Length(max: 255)]
        private string $avatarUrl,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}
