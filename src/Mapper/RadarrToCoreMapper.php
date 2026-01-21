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
use MartinCamen\ArrCore\ValueObject\ArrFileSize;
use MartinCamen\ArrCore\ValueObject\ArrId;
use MartinCamen\ArrCore\ValueObject\Duration;
use MartinCamen\ArrCore\ValueObject\Progress;
use MartinCamen\Radarr\Data\Responses\Download;
use MartinCamen\Radarr\Data\Responses\DownloadPage;
use MartinCamen\Radarr\Data\Responses\Movie as RadarrMovie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/**
 * Maps Radarr DTOs to php-arr-core domain models.
 *
 * This class provides pure, deterministic transformations from
 * Radarr-specific data structures to canonical core models.
 */
final class RadarrToCoreMapper extends ServiceToCoreMapper
{
    /** Map Radarr Movie DTO to Core Movie model */
    public static function mapMovie(RadarrMovie $radarrMovie): Movie
    {
        return new Movie(
            id: ArrId::fromInt($radarrMovie->id),
            title: $radarrMovie->title,
            year: $radarrMovie->year,
            status: StatusNormalizer::mediaFromRadarr($radarrMovie->status, $radarrMovie->hasFile),
            monitored: $radarrMovie->monitored,
            source: Service::Radarr,
            sizeOnDisk: ArrFileSize::fromBytes($radarrMovie->sizeOnDisk),
            path: $radarrMovie->path,
            overview: $radarrMovie->overview,
            posterUrl: self::extractImage($radarrMovie->images, 'poster'),
            fanartUrl: self::extractImage($radarrMovie->images, 'fanart'),
            imdbId: $radarrMovie->imdbId,
            tmdbId: $radarrMovie->tmdbId,
            runtime: $radarrMovie->runtime !== null ? Duration::fromMinutes($radarrMovie->runtime) : null,
            hasFile: $radarrMovie->hasFile,
        );
    }

    /**
     * Map Radarr Movie collection to array of Core Movies.
     *
     * @return array<int|string, Movie>
     */
    public static function mapMovieCollection(MovieCollection $movieCollection): array
    {
        return array_map(
            self::mapMovie(...),
            $movieCollection->all(),
        );
    }

    /** Map Radarr Download to Core DownloadItem */
    public static function mapDownload(Download $download): DownloadItem
    {
        $size = $download->size;
        $sizeLeft = $download->sizeLeft;
        $progress = (int) $download->getProgress();

        return new DownloadItem(
            id: ArrId::fromInt($download->id),
            name: $download->title ?? 'Unknown',
            size: ArrFileSize::fromBytes((int) $size),
            sizeRemaining: ArrFileSize::fromBytes((int) $sizeLeft),
            progress: Progress::fromPercentage($progress),
            status: StatusNormalizer::downloadFromRadarrQueue(
                $download->status,
                $download->trackedDownloadStatus,
                $download->trackedDownloadState,
            ),
            source: Service::Radarr,
            eta: $download->timeLeft !== null ? self::parseTimeSpan($download->timeLeft) : null,
            downloadClient: $download->downloadClient,
            indexer: $download->indexer,
            outputPath: $download->outputPath,
            mediaId: $download->movieId !== null ? ArrId::fromInt($download->movieId) : null,
            mediaTitle: $download->title,
            errorMessage: $download->errorMessage,
        );
    }

    /** Map Radarr DownloadPage to Core DownloadItemCollection */
    public static function mapDownloadPage(DownloadPage $downloadPage): DownloadItemCollection
    {
        $items = array_map(
            self::mapDownload(...),
            $downloadPage->all(),
        );

        return new DownloadItemCollection(...$items);
    }

    /**
     * Map Radarr SystemSummary to Core SystemSummary.
     *
     * @param array<int, HealthCheck> $healthChecks
     */
    public static function mapSystemSummary(DownloadServiceSystemSummary $downloadServiceSystemSummary, array $healthChecks = []): SystemSummary
    {
        return self::mapToSystemSummary(Service::Radarr, $downloadServiceSystemSummary, $healthChecks);
    }
}
