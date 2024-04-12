<?php

namespace App\Tests\Unit\Services\Import\Command;

use App\Services\Import\Command\ImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommandTest extends TestCase
{
    public function testConstruct()
    {
        $style = $this->createMock(SymfonyStyle::class);

        $command = new ImportCommand($style, 'test');
        $this->assertSame($style, $command->getStyle());
        $this->assertSame('test', $command->getHandle());
    }
}
