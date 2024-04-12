<?php

namespace App\Repository;

use App\Dto\EventInsertInput;
use App\Dto\EventInput;

interface WriteEventRepository
{
    public function upsert(EventInsertInput $input): void;
    public function update(EventInput $authorInput, int $id): void;
}
