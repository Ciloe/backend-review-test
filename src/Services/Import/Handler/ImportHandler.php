<?php

namespace App\Services\Import\Handler;

use App\Dto\ActorInsertInput;
use App\Dto\EventInsertInput;
use App\Dto\RepoInsertInput;
use App\Entity\EventType;
use App\Repository\DbalWriteEventRepository;
use App\Services\Import\Command\ImportCommand;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportHandler
{
    public function __construct(private DbalWriteEventRepository $dbal)
    {
    }

    public function __invoke(ImportCommand $query): void
    {
        $memoryLimit = (int)\ini_get('memory_limit') * 1024 * 1024;
        $style = $query->getStyle();

        $style->info('Start importing content');
        $style->progressStart();

        while (($element = \stream_get_line($query->getHandle(), PHP_INT_MAX, "\n")) !== false) {
            $memoryUsed = \memory_get_usage();
            if ($memoryUsed > $memoryLimit * 0.7) {
                \gc_collect_cycles();
            }
            $line = \json_decode($element, true);
            if (!\in_array($line['type'], EventType::$allGH)) {
                unset($line);
                continue;
            }

            $line = self::createEventInput(self::parseJson($line));
            $this->dbal->upsert($line);

            unset($line);
            $style->progressAdvance();
        }

        \gzclose($query->getHandle());
        $style->progressFinish();
    }

    private static function parseJson(array $line): array
    {
        return [
            'id' => (int)$line['id'],
            'payload' => $line['payload'],
            'comment' => $line['comment'] ?? null,
            'type' => \array_flip(EventType::$allGH)[$line['type']],
            'created_at' => new DateTimeImmutable($line['created_at']),
            'repo' => new RepoInsertInput(
                id: $line['repo']['id'],
                name: $line['repo']['name'],
                url: $line['repo']['url'],
            ),
            'actor' => new ActorInsertInput(
                id: $line['actor']['id'],
                login: $line['actor']['login'],
                url: $line['actor']['url'],
                avatarUrl: $line['actor']['avatar_url'],
            ),
        ];
    }

    private static function createEventInput(array $e): EventInsertInput
    {
        return new EventInsertInput(
            id: $e['id'],
            type: $e['type'],
            actor: $e['actor'],
            repo: $e['repo'],
            payload: $e['payload'],
            createdAt: $e['created_at'],
            comment: $e['comment'],
        );
    }
}
