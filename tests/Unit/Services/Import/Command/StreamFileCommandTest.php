<?php

namespace App\Tests\Unit\Services\Import\Command;

use App\Services\Import\Command\StreamFileCommand;
use PHPUnit\Framework\TestCase;

class StreamFileCommandTest extends TestCase
{
    public function testConstruct()
    {
        $service = new StreamFileCommand('test');
        $this->assertSame('test', $service->getFile());
    }
}
