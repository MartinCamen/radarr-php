<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum ReleaseEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'release';
    case ById = 'release/{id}';
    case Push = 'release/push';

    public function defaultResponse(): array
    {
        return match ($this) {
            self::All, self::ById => [],
            self::Push            => [],
        };
    }
}
