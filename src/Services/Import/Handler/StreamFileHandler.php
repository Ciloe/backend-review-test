<?php

namespace App\Services\Import\Handler;

use App\Services\Import\Command\StreamFileCommand;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StreamFileHandler
{
    public function __invoke(StreamFileCommand $query)
    {
        try {
            $handle = \gzopen($query->getFile(), 'r');
            if ($handle === false) {
                throw new Exception('Invalid file to open');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $handle;
    }
}
