<?php

namespace App\Services\Common;

use App\Services\Common\Contract\QueryBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function query(object $query): mixed
    {
        return $this->handle($query);
    }
}
