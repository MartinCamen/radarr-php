<?php

declare(strict_types=1);

namespace MartinCamen\Radarr;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Client\RadarrApiClient;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\Config\RadarrConfiguration;

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
 * // Get all movies
 * $movies = $radarr->movies()->all();
 * $movie = $radarr->movies()->find(123);
 *
 * // Get all downloads
 * $downloads = $radarr->downloads()->all();
 * $status = $radarr->downloads()->status();
 *
 * // Get system status
 * $status = $radarr->system()->status();
 * ```
 */
class Radarr implements RadarrInterface
{
    public function __construct(private readonly RadarrApiClientInterface $radarrApiClient) {}

    /** Create a new Radarr instance from connection parameters */
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

    /** Create a new Radarr instance from a configuration object */
    public static function make(RadarrConfiguration $radarrConfiguration): self
    {
        return new self(RadarrApiClient::make($radarrConfiguration));
    }

    /**
     * Access movie functionality.
     *
     * Provides access to list, search, add, update, and delete movies.
     */
    public function movies(): MovieActions
    {
        return $this->radarrApiClient->movies();
    }

    /**
     * Access download functionality (queue items).
     *
     * Provides access to view and manage active downloads.
     */
    public function downloads(): DownloadActions
    {
        return $this->radarrApiClient->downloads();
    }

    /**
     * Access system functionality.
     *
     * Provides access to system information, health information, and more.
     */
    public function system(): SystemActions
    {
        return $this->radarrApiClient->system();
    }

    /**
     * Access calendar functionality.
     *
     * Provides access to upcoming movie releases and calendar events.
     */
    public function calendar(): CalendarActions
    {
        return $this->radarrApiClient->calendar();
    }

    /**
     * Access history functionality.
     *
     * Provides access to download history, grabbed items, and import events.
     */
    public function history(): HistoryActions
    {
        return $this->radarrApiClient->history();
    }

    /**
     * Access wanted functionality.
     *
     * Provides access to missing movies and movies below quality cutoff.
     */
    public function wanted(): WantedActions
    {
        return $this->radarrApiClient->wanted();
    }

    /**
     * Access command functionality.
     *
     * Allows execution of Radarr commands like movie search, refresh, etc.
     */
    public function command(): CommandActions
    {
        return $this->radarrApiClient->command();
    }

    /**
     * Get the underlying API client for advanced operations.
     *
     * Use this when you need direct access to Radarr API functionality
     * that is not yet exposed through the high-level SDK methods.
     *
     * @example
     * ```php
     * // Access the raw downloads API with full options
     * $downloadPage = $radarr->api()->downloads()->all($pagination, $sort, $filters);
     *
     * // Add a movie using the raw API
     * $radarr->api()->movies()->add($movieData);
     * ```
     */
    public function api(): RadarrApiClientInterface
    {
        return $this->radarrApiClient;
    }
}
