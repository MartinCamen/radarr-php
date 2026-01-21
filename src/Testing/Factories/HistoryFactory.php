<?php

namespace MartinCamen\Radarr\Testing\Factories;

use MartinCamen\ArrCore\Testing\Factories\ArrHistoryFactory;

class HistoryFactory extends ArrHistoryFactory
{
    /** @return array<string, mixed> */
    protected static function getServiceDefaults(int $id): array
    {
        return [
            'movieId'     => $id,
            'sourceTitle' => "Test.Movie.{$id}.2024.1080p.BluRay.mkv",
            'data'        => [
                'indexer'        => 'NZBGeek',
                'downloadClient' => 'SABnzbd',
            ],
            'movie' => MovieFactory::make($id),
        ];
    }
}
