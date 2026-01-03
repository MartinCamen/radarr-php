<?php

namespace MartinCamen\Radarr\Testing\Factories;

use MartinCamen\PhpFileSize\FileSize;

class MovieFactory
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function make(int $id = 1, array $overrides = []): array
    {
        return array_merge([
            'id'                => $id,
            'title'             => "Test Movie {$id}",
            'sortTitle'         => "test movie {$id}",
            'originalTitle'     => "Test Movie {$id}",
            'originalLanguage'  => ['id' => 1, 'name' => 'English'],
            'year'              => 2024,
            'tmdbId'            => 100000 + $id,
            'imdbId'            => "tt000000{$id}",
            'status'            => 'released',
            'overview'          => "This is a test movie overview for movie {$id}.",
            'monitored'         => true,
            'hasFile'           => false,
            'path'              => "/movies/Test Movie {$id} ({$id})",
            'qualityProfileId'  => 1,
            'runtime'           => 120,
            'added'             => '2024-01-01T00:00:00Z',
            'inCinemas'         => '2024-06-01T00:00:00Z',
            'physicalRelease'   => '2024-09-01T00:00:00Z',
            'digitalRelease'    => '2024-08-15T00:00:00Z',
            'genres'            => ['Action', 'Drama'],
            'ratings'           => ['tmdb' => ['votes' => 1000, 'value' => 7.5]],
            'movieFile'         => null,
            'images'            => [],
            'alternativeTitles' => [],
            'sizeOnDisk'        => 0,
            'isAvailable'       => true,
            'folderName'        => "Test Movie {$id} (2024)",
        ], $overrides);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function makeMany(int $count = 5): array
    {
        $movies = [];

        for ($i = 1; $i <= $count; $i++) {
            $movies[] = self::make($i);
        }

        return $movies;
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeDownloaded(int $id = 1, array $overrides = []): array
    {
        $fileSize = (new FileSize())->gigabytes(4.5)->toBytes();

        return self::make($id, array_merge([
            'hasFile'    => true,
            'sizeOnDisk' => $fileSize,
            'movieFile'  => [
                'id'           => $id,
                'movieId'      => $id,
                'relativePath' => "Test.Movie.{$id}.2024.1080p.BluRay.mkv",
                'size'         => $fileSize,
                'quality'      => ['quality' => ['name' => 'Bluray-1080p']],
            ],
        ], $overrides));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function makeUnmonitored(int $id = 1, array $overrides = []): array
    {
        return self::make($id, array_merge([
            'monitored' => false,
        ], $overrides));
    }
}
