<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\SDK;

use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie;
use MartinCamen\ArrCore\Domain\System\SystemStatus;
use MartinCamen\Radarr\Mapper\RadarrToCoreMapper;
use MartinCamen\Radarr\Radarr;

/**
 * Radarr SDK client that returns Core domain models.
 *
 * This is the primary interface for interacting with Radarr.
 * All methods return canonical php-arr-core domain objects,
 * providing a unified API across all *arr services.
 */
final readonly class RadarrClient
{
    public function __construct(
        private Radarr $radarr,
    ) {}

    /**
     * Create a new RadarrClient from connection parameters.
     */
    public static function create(
        string $host = 'localhost',
        int $port = 7878,
        string $apiKey = '',
        bool $useHttps = false,
        int $timeout = 30,
        string $urlBase = '',
    ): self {
        return new self(
            new Radarr(
                host: $host,
                port: $port,
                apiKey: $apiKey,
                useHttps: $useHttps,
                timeout: $timeout,
                urlBase: $urlBase,
            ),
        );
    }

    /**
     * Get all active downloads.
     */
    public function downloads(): DownloadItemCollection
    {
        $queue = $this->radarr->queue()->all();

        return RadarrToCoreMapper::mapQueuePage($queue);
    }

    /**
     * Get all movies.
     *
     * @return array<int, Movie>
     */
    public function movies(): array
    {
        $movies = $this->radarr->movie()->all();

        return RadarrToCoreMapper::mapMovieCollection($movies);
    }

    /**
     * Get a single movie by ID.
     */
    public function movie(int $id): Movie
    {
        $movie = $this->radarr->movie()->get($id);

        return RadarrToCoreMapper::mapMovie($movie);
    }

    /**
     * Get system status.
     */
    public function systemStatus(): SystemStatus
    {
        $status = $this->radarr->system()->status();
        $health = $this->radarr->system()->health();

        return RadarrToCoreMapper::mapSystemStatus($status, $health->all());
    }
}
