<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Testing;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie as CoreMovie;
use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemSummary;
use MartinCamen\ArrCore\Domain\System\HealthCheckCollection;
use MartinCamen\ArrCore\Domain\System\SystemSummary;
use MartinCamen\ArrCore\Testing\BaseFake;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Mapper\RadarrToCoreMapper;
use MartinCamen\Radarr\RadarrInterface;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;
use MartinCamen\Radarr\Testing\Factories\SystemStatusFactory;

/**
 * Fake implementation for testing.
 *
 * Provides the same interface as the Radarr SDK but allows
 * custom responses and tracks method calls for assertions.
 *
 * @example
 * ```php
 * $fake = new RadarrFake([
 *     'movies' => MovieFactory::makeMany(5),
 * ]);
 *
 * $movies = $fake->movies();
 * $fake->assertCalled('movies');
 * ```
 */
final class RadarrFake extends BaseFake implements RadarrInterface
{
    private ?RadarrApiFake $apiFake = null;

    /**
     * Get all active downloads.
     */
    public function downloads(): DownloadItemCollection
    {
        $this->recordCall('downloads', []);

        $queueData = $this->formatDownloads();

        return RadarrToCoreMapper::mapQueuePage(
            QueuePage::fromArray($queueData),
        );
    }

    /**
     * Get all movies.
     *
     * @return array<int|string, CoreMovie>
     */
    public function movies(): array
    {
        $this->recordCall('movies', []);

        $movies = $this->responses['movies'] ?? MovieFactory::makeMany(3);

        return RadarrToCoreMapper::mapMovieCollection(
            MovieCollection::fromArray($movies),
        );
    }

    /**
     * Get a single movie by ID.
     */
    public function movie(int $id): CoreMovie
    {
        $this->recordCall('movie', ['id' => $id]);

        $movie = match (true) {
            isset($this->responses["movie/{$id}"]) => $this->responses["movie/{$id}"],
            isset($this->responses['movie'])       => $this->responses['movie'],
            default                                => MovieFactory::make($id),
        };

        return RadarrToCoreMapper::mapMovie(Movie::fromArray($movie));
    }

    /**
     * Get system status.
     */
    public function system(): SystemActions
    {
        $this->recordCall('system', []);

        return $this->api()->system();
    }

    /**
     * Get system summary.
     */
    public function systemSummary(): SystemSummary
    {
        $this->recordCall('systemSummary', []);

        $status = $this->getStatusForDownloadServiceSystemSummary();
        $health = $this->getHealthForDownloadServiceSystemSummary();

        return RadarrToCoreMapper::mapSystemSummary($status, $health->all());
    }

    /**
     * Access calendar functionality.
     */
    public function calendar(): CalendarActions
    {
        $this->recordCall('calendar', []);

        return $this->api()->calendar();
    }

    /**
     * Access history functionality.
     */
    public function history(): HistoryActions
    {
        $this->recordCall('history', []);

        return $this->api()->history();
    }

    /**
     * Access wanted functionality.
     */
    public function wanted(): WantedActions
    {
        $this->recordCall('wanted', []);

        return $this->api()->wanted();
    }

    /**
     * Access command functionality.
     */
    public function command(): CommandActions
    {
        $this->recordCall('command', []);

        return $this->api()->command();
    }

    /**
     * Get the underlying API client fake for advanced operations.
     */
    public function api(): RadarrApiClientInterface
    {
        return $this->apiFake ??= new RadarrApiFake();
    }

    /** @return array<string, mixed> */
    protected function formatDownloads(): array
    {
        return $this->responses['downloads'] ?? [
            'page'         => 1,
            'pageSize'     => 10,
            'totalRecords' => 2,
            'records'      => DownloadFactory::makeMany(2),
        ];
    }

    protected function getStatusForDownloadServiceSystemSummary(): DownloadServiceSystemSummary
    {
        $data = $this->responses['systemSummary'] ?? SystemStatusFactory::make();

        return DownloadServiceSystemSummary::fromArray($data);
    }

    protected function getHealthForDownloadServiceSystemSummary(): HealthCheckCollection
    {
        $data = isset($this->responses['systemSummary'])
            ? $this->responses['health']
            : [];

        return HealthCheckCollection::fromArray($data ?? []);
    }
}
