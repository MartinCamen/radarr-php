<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum CommandEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'command';
    case ById = 'command/{id}';

    public function defaultResponse(): mixed
    {
        return match ($this) {
            self::All  => [],
            self::ById => [],
        };
    }
}
