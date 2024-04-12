<?php

namespace App\Dto;

use DateTimeInterface;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class EventInsertInput
{
    public function __construct(
        private int $id,
        #[Assert\Length(min: 5, max: 5)]
        private string $type,
        private ActorInsertInput $actor,
        private RepoInsertInput $repo,
        private array $payload,
        private DateTimeImmutable $createdAt,
        #[Assert\Length(min: 20)]
        private ?string $comment,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getActor(): ActorInsertInput
    {
        return $this->actor;
    }

    public function getRepo(): RepoInsertInput
    {
        return $this->repo;
    }

    public function getPayload(): string
    {
        return json_encode($this->payload);
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format(DateTimeInterface::ATOM);
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
