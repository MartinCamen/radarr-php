<?php

namespace MartinCamen\Radarr\Testing\Factories;

use MartinCamen\ArrCore\Testing\Factories\ArrSystemStatusFactory;

class SystemStatusFactory extends ArrSystemStatusFactory
{
    /** @return array<string, mixed> */
    protected static function getServiceDefaults(): array
    {
        return [
            'appName'          => 'Radarr',
            'instanceName'     => 'Radarr',
            'version'          => '5.0.0.0000',
            'startupPath'      => '/app/radarr',
            'branch'           => 'master',
            'migrationVersion' => 100,
            'packageVersion'   => '5.0.0.0000',
            'packageAuthor'    => '[Team Radarr](https://radarr.video)',
        ];
    }
}
