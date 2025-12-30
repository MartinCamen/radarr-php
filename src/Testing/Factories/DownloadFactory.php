<?php

namespace MartinCamen\Radarr\Testing\Factories;

use MartinCamen\ArrCore\Testing\Factories\ArrDownloadFactory;

class DownloadFactory extends ArrDownloadFactory
{
    /**
     * Get Radarr-specific default attributes.
     *
     * @return array<string, mixed>
     */
    protected static function getServiceDefaults(int $id): array
    {
        return [
            'movieId'       => $id,
            'title'         => "Test.Movie.{$id}.2024.1080p.BluRay.mkv",
            'size'          => 4500000000,
            'sizeleft'      => 2250000000,
            'timeleft'      => '01:30:00',
            'outputPath'    => '/downloads/complete/Test.Movie.' . $id,
            'customFormats' => [],
        ];
    }
}
