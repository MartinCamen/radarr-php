<?php

namespace MartinCamen\Radarr\Data\Enums;

enum HistoryEventType: int
{
    case Unknown = 0;
    case Grabbed = 1;
    case DownloadFolderImported = 3;
    case DownloadFailed = 4;
    case MovieFileDeleted = 6;
    case MovieFolderImported = 7;
    case MovieFileRenamed = 8;
    case DownloadIgnored = 9;
}
