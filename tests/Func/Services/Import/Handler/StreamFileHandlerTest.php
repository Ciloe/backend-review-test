<?php

namespace App\Tests\Func\Services\Import\Handler;

use App\Services\Import\Command\StreamFileCommand;
use App\Services\Import\Handler\StreamFileHandler;
use Exception;
use PHPUnit\Framework\TestCase;

class StreamFileHandlerTest extends TestCase
{
    const TEST_FILE = 'test-import.json.gz';
    const TEST_FILE_PATH = __DIR__ .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . self::TEST_FILE;

    public function test__invoke()
    {
        $command = new StreamFileCommand(self::TEST_FILE_PATH);
        $service = new StreamFileHandler();

        $result = $service($command);
        $this->assertIsResource($result);
    }

    public function test__invokeWithException()
    {
        $command = new StreamFileCommand(self::TEST_FILE);
        $service = new StreamFileHandler();

        $this->expectException(Exception::class);
        $service($command);
    }
}
