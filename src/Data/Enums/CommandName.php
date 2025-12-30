<?php

namespace MartinCamen\Radarr\Data\Enums;

enum CommandName: string
{
    case RefreshMovie = 'RefreshMovie';
    case RescanMovie = 'RescanMovie';
    case MoviesSearch = 'MoviesSearch';
    case DownloadedMoviesScan = 'DownloadedMoviesScan';
    case RssSync = 'RssSync';
    case RenameMovie = 'RenameMovie';
    case RenameFiles = 'RenameFiles';
    case Backup = 'Backup';
    case MissingMoviesSearch = 'MissingMoviesSearch';
    case CutoffUnmetMoviesSearch = 'CutoffUnmetMoviesSearch';
    case ManualImport = 'ManualImport';
    case InteractiveImport = 'InteractiveImport';
}
