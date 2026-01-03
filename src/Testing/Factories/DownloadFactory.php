<?php

namespace MartinCamen\Radarr\Testing\Factories;

use MartinCamen\ArrCore\Testing\Factories\ArrDownloadFactory;
use MartinCamen\PhpFileSize\FileSize;

class DownloadFactory extends ArrDownloadFactory
{
    /**
     * Get Radarr-specific default attributes.
     *
     * @return array<string, mixed>
     */
    protected static function getServiceDefaults(int $id): array
    {
        $fileSize = new FileSize();

        return [
            'movieId'       => $id,
            'title'         => "Test.Movie.{$id}.2024.1080p.BluRay.mkv",
            'size'          => $fileSize->gigabytes(4.5)->toBytes(),
            'sizeleft'      => $fileSize->gigabytes(2.25)->toBytes(),
            'timeleft'      => '01:30:00',
            'outputPath'    => '/downloads/complete/Test.Movie.' . $id,
            'customFormats' => [],
        ];
    }
}
