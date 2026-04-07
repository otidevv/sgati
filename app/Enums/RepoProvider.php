<?php

namespace App\Enums;

enum RepoProvider: string
{
    case GitHub    = 'github';
    case GitLab    = 'gitlab';
    case Bitbucket = 'bitbucket';
    case Gitea     = 'gitea';
    case Other     = 'other';

    public function label(): string
    {
        return match($this) {
            self::GitHub    => 'GitHub',
            self::GitLab    => 'GitLab',
            self::Bitbucket => 'Bitbucket',
            self::Gitea     => 'Gitea',
            self::Other     => 'Otro',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::GitHub    => 'slate',
            self::GitLab    => 'orange',
            self::Bitbucket => 'blue',
            self::Gitea     => 'teal',
            self::Other     => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::GitHub    => 'github',
            self::GitLab    => 'gitlab',
            self::Bitbucket => 'bitbucket',
            self::Gitea     => 'gitea',
            self::Other     => 'git',
        };
    }
}
