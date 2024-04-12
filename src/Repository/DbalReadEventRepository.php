<?php

namespace App\Repository;

use App\Dto\SearchInput;
use Doctrine\DBAL\Connection;

class DbalReadEventRepository implements ReadEventRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function countAll(SearchInput $searchInput): int
    {
        $sql = <<<SQL
        SELECT sum(count) as count
        FROM event
        WHERE date(created_at) = :date
        AND payload::text like (:keyword)::text
SQL;

        return (int) $this->connection->fetchOne($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => sprintf('%%%s%%', $searchInput->keyword),
        ]);
    }

    public function countByType(SearchInput $searchInput): array
    {
        $sql = <<<'SQL'
            SELECT type, sum(count) as count
            FROM event
            WHERE date(created_at) = :date
            AND payload::text like (:keyword)::text
            GROUP BY type
SQL;

        return $this->connection->fetchAllKeyValue($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => sprintf('%%%s%%', $searchInput->keyword),
        ]);
    }

    public function statsByTypePerHour(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT extract(hour from created_at) as hour, type, sum(count) as count
            FROM event
            WHERE date(created_at) = :date
            AND payload::text like (:keyword)::text
            GROUP BY TYPE, EXTRACT(hour from created_at)
SQL;

        $stats = $this->connection->fetchAllAssociative($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => sprintf('%%%s%%', $searchInput->keyword),
        ]);

        $data = array_fill(0, 24, ['commit' => 0, 'pullRequest' => 0, 'comment' => 0]);

        foreach ($stats as $stat) {
            $data[(int) $stat['hour']][$stat['type']] = $stat['count'];
        }

        return $data;
    }

    public function getLatest(SearchInput $searchInput): array
    {
        $sql = <<<SQL
            SELECT e.type, jsonb_build_object('id', r.id, 'name', r.name, 'url', r.url) AS repo
            FROM event e
            INNER JOIN repo r ON e.repo_id = r.id
            WHERE date(e.created_at) = :date
            AND e.payload::text like (:keyword)::text
SQL;

        $result = $this->connection->fetchAllAssociative($sql, [
            'date' => $searchInput->date->format('Y-m-d'),
            'keyword' => sprintf('%%%s%%', $searchInput->keyword),
        ]);

        $result = array_map(static function($item) {
            $item['repo'] = json_decode($item['repo'], true);

            return $item;
        }, $result);

        return $result;
    }

    public function exist(int $id): bool
    {
        $sql = <<<SQL
            SELECT 1
            FROM event
            WHERE id = :id
        SQL;

        $result = $this->connection->fetchOne($sql, [
            'id' => $id
        ]);

        return (bool) $result;
    }
}
