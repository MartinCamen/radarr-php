<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Mapper;

use MartinCamen\ArrCore\Domain\Download\DownloadItem;
use MartinCamen\ArrCore\Domain\Download\DownloadItemCollection;
use MartinCamen\ArrCore\Domain\Media\Movie;
use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemStatus;
use MartinCamen\ArrCore\Domain\System\HealthCheck;
use MartinCamen\ArrCore\Domain\System\HealthIssue;
use MartinCamen\ArrCore\Domain\System\SystemStatus;
use MartinCamen\ArrCore\Enum\Service;
use MartinCamen\ArrCore\Mapping\StatusNormalizer;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use MartinCamen\ArrCore\ValueObject\Timestamp;
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
final class RadarrToCoreMapper
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
     * @return array<int, Movie>
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
     * Map Radarr SystemStatus to Core SystemStatus.
     *
     * @param array<int, HealthCheck> $healthChecks
     */
    public static function mapSystemStatus(DownloadServiceSystemStatus $dto, array $healthChecks = []): SystemStatus
    {
        $issues = array_map(
            fn(HealthCheck $check): HealthIssue => new HealthIssue(
                type: $check->type,
                message: $check->message,
                source: $check->source,
                wikiUrl: $check->wikiUrl,
            ),
            $healthChecks,
        );

        return new SystemStatus(
            source: Service::Radarr,
            version: $dto->version,
            isHealthy: count($issues) === 0,
            startTime: $dto->startTime !== '' ? Timestamp::fromString($dto->startTime) : null,
            branch: $dto->branch,
            runtimeVersion: $dto->runtimeVersion,
            osName: $dto->osName,
            healthIssues: $issues,
        );
    }

    /**
     * Parse a .NET TimeSpan string (d.hh:mm:ss or hh:mm:ss) to Duration.
     */
    private static function parseTimeSpan(?string $timeSpan): ?Duration
    {
        if ($timeSpan === null || $timeSpan === '') {
            return null;
        }

        $seconds = 0;

        // Handle format: d.hh:mm:ss or hh:mm:ss
        if (str_contains($timeSpan, '.')) {
            [$days, $time] = explode('.', $timeSpan, 2);
            $seconds += (int) $days * 86400;
            $timeSpan = $time;
        }

        $parts = explode(':', $timeSpan);
        if (count($parts) === 3) {
            $seconds += (int) $parts[0] * 3600;
            $seconds += (int) $parts[1] * 60;
            $seconds += (int) $parts[2];
        }

        return Duration::fromSeconds($seconds);
    }

    /**
     * Extract image URL from images array.
     *
     * @param array<int, array<string, mixed>> $images
     */
    private static function extractImage(array $images, string $type): ?string
    {
        foreach ($images as $image) {
            if (($image['coverType'] ?? '') === $type) {
                return $image['remoteUrl'] ?? $image['url'] ?? null;
            }
        }

        return null;
    }
}
