<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum QueueEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case All = 'queue';
    case ById = 'queue/{id}';
    case Details = 'queue/details';
    case Status = 'queue/status';
    case Bulk = 'queue/bulk';

    public function defaultResponse(): mixed
    {
        return match ($this) {
            self::All                   => ['page' => 1, 'pageSize' => 10, 'totalRecords' => 0, 'records' => []],
            self::ById, self::Bulk      => null,
            self::Details, self::Status => [],
        };
    }
}
