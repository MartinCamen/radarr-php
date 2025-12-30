<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum MovieEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'movie';
    case ById = 'movie/{id}';
    case Lookup = 'movie/lookup';
    case LookupTmdb = 'movie/lookup/tmdb';
    case LookupImdb = 'movie/lookup/imdb';

    public function defaultResponse(): mixed
    {
        return match ($this) {
            self::All, self::Lookup, self::LookupTmdb, self::LookupImdb => [],
            self::ById => [],
        };
    }
}
