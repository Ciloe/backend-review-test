<?php

namespace App\Services\Common\Contract;

interface QueryBusInterface
{
    public function query(object $query): mixed;
}
