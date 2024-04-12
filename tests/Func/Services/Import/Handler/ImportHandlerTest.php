<?php

namespace App\Tests\Func\Services\Import\Handler;

use App\Dto\ActorInsertInput;
use App\Dto\EventInsertInput;
use App\Dto\RepoInsertInput;
use App\Repository\DbalWriteEventRepository;
use App\Services\Import\Command\ImportCommand;
use App\Services\Import\Command\StreamFileCommand;
use App\Services\Import\Handler\ImportHandler;
use App\Services\Import\Handler\StreamFileHandler;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportHandlerTest extends TestCase
{
    public function test__invoke()
    {
        $streamCommand = new StreamFileCommand(StreamFileHandlerTest::TEST_FILE_PATH);
        $handle = (new StreamFileHandler())($streamCommand);

        $expected1 = new EventInsertInput(
            id: 19543141328,
            type: 'PR',
            actor: new ActorInsertInput(
                id: 48,
                login: 'anonymous2',
                url: 'https://api.github.com/users/anonymous2',
                avatarUrl: 'https://avatars.githubusercontent.com/u/48?',
            ),
            repo: new RepoInsertInput(
                id: 23,
                name: 'anonymous2/hello-world',
                url: 'https://api.github.com/repos/anonymous2/hello-world',
            ),
            payload: ['test' => 'test'],
            createdAt: new DateTimeImmutable('2022-01-01T10:00:02Z'),
            comment: null,
        );
        $expected2 = new EventInsertInput(
            id: 19543143002,
            type: 'COM',
            actor: new ActorInsertInput(
                id: 35,
                login: 'anonymous3',
                url: 'https://api.github.com/users/anonymous3',
                avatarUrl: 'https://avatars.githubusercontent.com/u/35?',
            ),
            repo: new RepoInsertInput(
                id: 28,
                name: 'anonymous3/hello-world',
                url: 'https://api.github.com/repos/anonymous3/hello-world',
            ),
            payload: ['test' => 'test'],
            createdAt: new DateTimeImmutable('2022-01-01T10:00:23Z'),
            comment: 'test',
        );
        $expected3 = new EventInsertInput(
            id: 19543141342,
            type: 'MSG',
            actor: new ActorInsertInput(
                id: 41,
                login: 'anonymous4',
                url: 'https://api.github.com/users/anonymous4',
                avatarUrl: 'https://avatars.githubusercontent.com/u/41?',
            ),
            repo: new RepoInsertInput(
                id: 43,
                name: 'anonymous4/hello-world',
                url: 'https://api.github.com/repos/anonymous4/hello-world',
            ),
            payload: ['test' => 'test'],
            createdAt: new DateTimeImmutable('2022-01-01T10:00:02Z'),
            comment: null,
        );

        $style = $this->createMock(SymfonyStyle::class);
        $dbal = $this->createMock(DbalWriteEventRepository::class);
        $matcher = $this->exactly(3);
        $dbal->expects($matcher)
            ->method('upsert')
            ->willReturnCallback(fn () => match ($matcher->getInvocationCount() - 1) {
                0 => $expected1,
                1 => $expected2,
                2 => $expected3,
            });

        $query = new ImportCommand($style, $handle);
        $service = new ImportHandler($dbal);
        $service($query);
    }
}
