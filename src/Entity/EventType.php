<?php

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const COMMIT = 'COM';
    public const COMMENT = 'MSG';
    public const PULL_REQUEST = 'PR';

    public const GH_COMMIT = 'CommitCommentEvent';
    public const GH_COMMENT = 'IssueCommentEvent';
    public const GH_PULL_REQUEST = 'PullRequestEvent';

    public static array $allGH = [
        self::COMMIT => self::GH_COMMIT,
        self::COMMENT => self::GH_COMMENT,
        self::PULL_REQUEST => self::GH_PULL_REQUEST,
    ];

    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
    ];
}
