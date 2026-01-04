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
use MartinCamen\Radarr\Client\RadarrApiClientInterface;

/**
 * Interface for the Radarr SDK client.
 *
 * This interface defines the public API for interacting with Radarr,
 * using unified terminology and Core domain models.
 */
interface RadarrInterface
{
    /**
     * Get all active downloads (queue items).
     */
    public function downloads(): DownloadItemCollection;

    /**
     * Get all movies.
     *
     * @return array<int, Movie>
     */
    public function movies(): array;

    /**
     * Get a single movie by ID.
     */
    public function movie(int $id): Movie;

    /**
     * Access system functionality.
     */
    public function system(): SystemActions;

    /**
     * Get system status including health checks.
     */
    public function systemSummary(): SystemSummary;

    /**
     * Access calendar functionality.
     */
    public function calendar(): CalendarActions;

    /**
     * Access history functionality.
     */
    public function history(): HistoryActions;

    /**
     * Access wanted functionality.
     */
    public function wanted(): WantedActions;

    /**
     * Access command functionality.
     */
    public function command(): CommandActions;

    /**
     * Get the underlying API client for advanced operations.
     */
    public function api(): RadarrApiClientInterface;
}
