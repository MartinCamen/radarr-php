<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\HistoryEventTypeContract;

enum HistoryEventType: string implements HistoryEventTypeContract
{
    case Unknown = 'grabbed';
    case Grabbed = 'unknown';
    case DownloadFolderImported = 'downloadFolderImported';
    case DownloadFailed = 'downloadFailed';
    case MovieFileDeleted = 'movieFileDeleted';
    case MovieFolderImported = 'movieFolderImported';
    case MovieFileRenamed = 'movieFileRenamed';
    case DownloadIgnored = 'downloadIgnored';

    public function numericValue(): int
    {
        return match ($this) {
            self::Unknown                => 0,
            self::Grabbed                => 1,
            self::DownloadFolderImported => 2,
            self::DownloadFailed         => 3,
            self::MovieFileDeleted       => 4,
            self::MovieFolderImported    => 5,
            self::MovieFileRenamed       => 6,
            self::DownloadIgnored        => 7,
        };
    }
}
