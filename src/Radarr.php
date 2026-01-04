<?php

declare(strict_types=1);

namespace MartinCamen\Radarr;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie;
use MartinCamen\ArrCore\Domain\System\SystemSummary;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Client\RadarrApiClient;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\Config\RadarrConfiguration;
use MartinCamen\Radarr\Mapper\RadarrToCoreMapper;

/**
 * Radarr SDK client - the primary interface for interacting with Radarr.
 *
 * This class provides a unified API with Core domain models, making it
 * easy to work with Radarr data in a type-safe, cross-service compatible way.
 *
 * @example Basic usage:
 * ```php
 * $radarr = Radarr::create(
 *     host: 'localhost',
 *     port: 7878,
 *     apiKey: 'your-api-key',
 * );
 *
 * // Get all downloads (queue items)
 * $downloads = $radarr->downloads();
 *
 * // Get all movies
 * $movies = $radarr->movies();
 *
 * // Get system status
 * $status = $radarr->system()->status();
 * ```
 */
class Radarr implements RadarrInterface
{
    public function __construct(private readonly RadarrApiClientInterface $apiClient) {}

    /**
     * Create a new Radarr instance from connection parameters.
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
            new RadarrApiClient(
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
     * Create a new Radarr instance from a configuration object.
     */
    public static function make(RadarrConfiguration $config): self
    {
        return new self(RadarrApiClient::make($config));
    }

    /**
     * Get all active downloads (queue items).
     *
     * Returns a collection of download items mapped to Core domain models,
     * providing a unified interface across all *arr services.
     */
    public function downloads(): DownloadItemCollection
    {
        $queue = $this->apiClient->queue()->all();

        return RadarrToCoreMapper::mapQueuePage($queue);
    }

    /**
     * Get all movies.
     *
     * @return array<int|string, Movie>
     */
    public function movies(): array
    {
        $movies = $this->apiClient->movie()->all();

        return RadarrToCoreMapper::mapMovieCollection($movies);
    }

    /**
     * Get a single movie by ID.
     */
    public function movie(int $id): Movie
    {
        $movie = $this->apiClient->movie()->find($id);

        return RadarrToCoreMapper::mapMovie($movie);
    }

    /**
     * Access system functionality.
     *
     * Provides access to system information, health information, and more.
     */
    public function system(): SystemActions
    {
        return $this->apiClient->system();
    }

    /**
     * Get system status including health checks.
     */
    public function systemSummary(): SystemSummary
    {
        $status = $this->apiClient->system()->status();
        $health = $this->apiClient->system()->health();

        return RadarrToCoreMapper::mapSystemSummary($status, $health->all());
    }

    /**
     * Access calendar functionality.
     *
     * Provides access to upcoming movie releases and calendar events.
     */
    public function calendar(): CalendarActions
    {
        return $this->apiClient->calendar();
    }

    /**
     * Access history functionality.
     *
     * Provides access to download history, grabbed items, and import events.
     */
    public function history(): HistoryActions
    {
        return $this->apiClient->history();
    }

    /**
     * Access wanted functionality.
     *
     * Provides access to missing movies and movies below quality cutoff.
     */
    public function wanted(): WantedActions
    {
        return $this->apiClient->wanted();
    }

    /**
     * Access command functionality.
     *
     * Allows execution of Radarr commands like movie search, refresh, etc.
     */
    public function command(): CommandActions
    {
        return $this->apiClient->command();
    }

    /**
     * Get the underlying API client for advanced operations.
     *
     * Use this when you need direct access to Radarr API functionality
     * that is not yet exposed through the high-level SDK methods.
     *
     * @example
     * ```php
     * // Access the raw queue API with full options
     * $queuePage = $radarr->api()->queue()->all($pagination, $sort, $filters);
     *
     * // Add a movie using the raw API
     * $radarr->api()->movie()->add($movieData);
     * ```
     */
    public function api(): RadarrApiClientInterface
    {
        return $this->apiClient;
    }
}
