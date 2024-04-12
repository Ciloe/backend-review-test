<?php

namespace App\Repository;

use App\Dto\EventCommentInput;

interface WriteEventRepository
{
    public function updateComment(EventCommentInput $authorInput, int $id): void;
}
