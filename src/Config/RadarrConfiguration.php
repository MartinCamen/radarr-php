<?php

namespace MartinCamen\Radarr\Config;

use MartinCamen\ArrCore\Config\ArrServiceConfiguration;
use MartinCamen\ArrCore\Contract\ArrServiceConfigurationContract;

class RadarrConfiguration extends ArrServiceConfiguration implements ArrServiceConfigurationContract
{
    public int $port = 7878;
    public string $apiVersion = 'v3';
}
