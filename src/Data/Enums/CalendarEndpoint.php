<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum CalendarEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'calendar';
    case ById = 'calendar/{id}';

    public function defaultResponse(): mixed
    {
        return match ($this) {
            self::All  => [],
            self::ById => [],
        };
    }
}
