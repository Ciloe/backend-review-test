<?php

namespace App\Repository;

use App\Dto\EventInput;
use App\Dto\EventInsertInput;
use Doctrine\DBAL\Connection;

class DbalWriteEventRepository implements WriteEventRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function upsert(EventInsertInput $input): void
    {
        $sqlInsertActor = <<<SQL
INSERT INTO actor (id, login, url, avatar_url)
VALUES (:id, :login, :url, :avatar_url)
ON CONFLICT (id)
    DO UPDATE SET login = :login, url = :url, avatar_url = :avatar_url
;
SQL;
        $sqlInsertRepo = <<<SQL
INSERT INTO repo (id, name, url)
VALUES (:id, :name, :url)
ON CONFLICT (id)
    DO UPDATE SET name = :name, url = :url
;
SQL;
        $sqlInsertEvent = <<<SQL
INSERT INTO event (id, actor_id, repo_id, type, count, payload, create_at, comment)
VALUES (:id, :actor_id, :repo_id, :type, 1, (:payload)::jsonb, :create_at, :comment)
ON CONFLICT (id)
    DO UPDATE SET type = :type, payload = :payload, create_at = :create_at, comment = :comment
;
SQL;
        $this->connection->executeQuery($sqlInsertRepo, [
            'id' => $input->getRepo()->getId(),
            'name' => $input->getRepo()->getName(),
            'url' => $input->getRepo()->getUrl(),
        ]);

        $this->connection->executeQuery($sqlInsertActor, [
            'id' => $input->getActor()->getId(),
            'login' => $input->getActor()->getLogin(),
            'url' => $input->getActor()->getUrl(),
            'avatar_url' => $input->getActor()->getAvatarUrl(),
        ]);

        $this->connection->executeQuery($sqlInsertEvent, [
            'id' => $input->getId(),
            'actor_id' => $input->getActor()->getId(),
            'repo_id' => $input->getRepo()->getId(),
            'type' => $input->getType(),
            'payload' => $input->getPayload(),
            'create_at' => $input->getCreatedAt(),
            'comment' => $input->getComment(),
        ]);
    }

    public function update(EventInput $authorInput, int $id): void
    {
        $sql = <<<SQL
        UPDATE event
        SET comment = :comment
        WHERE id = :id
SQL;

        $this->connection->executeQuery($sql, ['id' => $id, 'comment' => $authorInput->comment]);
    }
}
