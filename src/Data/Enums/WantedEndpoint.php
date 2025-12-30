<?php

namespace MartinCamen\Radarr\Data\Enums;

use MartinCamen\ArrCore\Contract\Endpoint;
use MartinCamen\ArrCore\Contract\ResolvesEndpointPath;

enum WantedEndpoint: string implements Endpoint
{
    use ResolvesEndpointPath;

    case Missing = 'wanted/missing';
    case Cutoff = 'wanted/cutoff';

    /** @return array<string, mixed> */
    public function defaultResponse(): array
    {
        return [
            'page'         => 1,
            'pageSize'     => 10,
            'totalRecords' => 0,
            'records'      => [],
        ];
    }
}
