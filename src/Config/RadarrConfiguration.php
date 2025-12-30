<?php

namespace MartinCamen\Radarr\Config;

use MartinCamen\ArrCore\Config\ArrServiceConfiguration;
use MartinCamen\ArrCore\Contract\ArrServiceConfigurationContract;

class RadarrConfiguration extends ArrServiceConfiguration implements ArrServiceConfigurationContract
{
    public static function getDefaultPort(): int
    {
        return 7878;
    }

    public static function getDefaultVersion(): string
    {
        return 'v3';
    }
}
