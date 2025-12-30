<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Testing;

use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie as CoreMovie;
use MartinCamen\ArrCore\Domain\System\SystemStatus as CoreSystemStatus;
use MartinCamen\ArrCore\Testing\BaseFake;
use MartinCamen\ArrCore\Testing\Traits\FakesArrDownloadServices;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Mapper\RadarrToCoreMapper;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;

/**
 * Fake implementation for testing.
 *
 * Provides the same interface as RadarrClient but allows
 * custom responses and tracks method calls for assertions.
 */
final class RadarrFake extends BaseFake
{
    use FakesArrDownloadServices;

    /**
     * Get all active downloads.
     */
    public function downloads(): DownloadItemCollection
    {
        $this->recordCall('downloads', []);

        $queueData = $this->formatsDownloads();

        return RadarrToCoreMapper::mapQueuePage(
            QueuePage::fromArray($queueData),
        );
    }

    /**
     * Get all movies.
     *
     * @return array<int, CoreMovie>
     */
    public function movies(): array
    {
        $this->recordCall('movies', []);

        $movies = $this->responses['movies'] ?? MovieFactory::makeMany(3);

        return RadarrToCoreMapper::mapMovieCollection($movies);
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
    public function systemStatus(): CoreSystemStatus
    {
        $this->recordCall('systemStatus', []);

        $status = $this->getStatusForDownloadServiceSystemStatus();
        $health = $this->getHealthForDownloadServiceSystemStatus();

        return RadarrToCoreMapper::mapSystemStatus($status, $health->all());
    }
}
