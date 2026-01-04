<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Mapper;

use MartinCamen\ArrCore\Domain\Download\DownloadItem;
use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie;
use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemSummary;
use MartinCamen\ArrCore\Domain\System\HealthCheck;
use MartinCamen\ArrCore\Domain\System\SystemSummary;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\Mapping\ServiceToCoreMapper;
use MartinCamen\ArrCore\Mapping\StatusNormalizer;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use MartinCamen\Radarr\Data\Responses\Movie as RadarrMovie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Data\Responses\QueueRecord;

/**
 * Maps Radarr DTOs to php-arr-core domain models.
 *
 * This class provides pure, deterministic transformations from
 * Radarr-specific data structures to canonical core models.
 */
final class RadarrToCoreMapper extends ServiceToCoreMapper
{
    /**
     * Map Radarr Movie DTO to Core Movie model.
     */
    public static function mapMovie(RadarrMovie $dto): Movie
    {
        return new Movie(
            id: ArrId::fromInt($dto->id),
            title: $dto->title,
            year: $dto->year,
            status: StatusNormalizer::mediaFromRadarr($dto->status, $dto->hasFile),
            monitored: $dto->monitored,
            source: Service::Radarr,
            sizeOnDisk: FileSize::fromBytes($dto->sizeOnDisk),
            path: $dto->path,
            overview: $dto->overview,
            posterUrl: self::extractImage($dto->images, 'poster'),
            fanartUrl: self::extractImage($dto->images, 'fanart'),
            imdbId: $dto->imdbId,
            tmdbId: $dto->tmdbId,
            runtime: $dto->runtime !== null ? Duration::fromMinutes($dto->runtime) : null,
            hasFile: $dto->hasFile,
        );
    }

    /**
     * Map Radarr Movie collection to array of Core Movies.
     *
     * @return array<int|string, Movie>
     */
    public static function mapMovieCollection(MovieCollection $collection): array
    {
        return array_map(
            self::mapMovie(...),
            $collection->all(),
        );
    }

    /**
     * Map Radarr Queue Record to Core DownloadItem.
     */
    public static function mapQueueRecord(QueueRecord $dto): DownloadItem
    {
        $size = $dto->size;
        $sizeLeft = $dto->sizeleft;
        $progress = $size > 0 ? (($size - $sizeLeft) / $size) * 100 : 0;

        return new DownloadItem(
            id: ArrId::fromInt($dto->id),
            name: $dto->title ?? 'Unknown',
            size: FileSize::fromBytes((int) $size),
            sizeRemaining: FileSize::fromBytes((int) $sizeLeft),
            progress: Progress::fromPercentage($progress),
            status: StatusNormalizer::downloadFromRadarrQueue(
                $dto->status,
                $dto->trackedDownloadStatus,
                $dto->trackedDownloadState,
            ),
            source: Service::Radarr,
            eta: $dto->timeleft !== null ? self::parseTimeSpan($dto->timeleft) : null,
            downloadClient: $dto->downloadClient,
            indexer: $dto->indexer,
            outputPath: $dto->outputPath,
            mediaId: $dto->movieId !== null ? ArrId::fromInt($dto->movieId) : null,
            mediaTitle: $dto->title,
            errorMessage: $dto->errorMessage,
        );
    }

    /**
     * Map Radarr Queue Page to Core DownloadItemCollection.
     */
    public static function mapQueuePage(QueuePage $dto): DownloadItemCollection
    {
        $items = array_map(
            self::mapQueueRecord(...),
            $dto->records(),
        );

        return new DownloadItemCollection(...$items);
    }

    /**
     * Map Radarr SystemSummary to Core SystemSummary.
     *
     * @param array<int, HealthCheck> $healthChecks
     */
    public static function mapSystemSummary(DownloadServiceSystemSummary $dto, array $healthChecks = []): SystemSummary
    {
        return self::mapToSystemSummary(Service::Radarr, $dto, $healthChecks);
    }
}
