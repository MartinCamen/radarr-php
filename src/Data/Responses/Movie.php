<?php

namespace MartinCamen\Radarr\Data\Responses;

final readonly class Movie
{
    /**
     * @param array<int, string> $genres
     * @param array<string, mixed> $ratings
     * @param array<string, mixed>|null $movieFile
     * @param array<int, array<string, mixed>> $images
     * @param array<int, array<string, mixed>> $alternativeTitles
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $sortTitle,
        public string $originalTitle,
        public string $originalLanguage,
        public ?int $year,
        public ?int $tmdbId,
        public ?string $imdbId,
        public string $status,
        public string $overview,
        public bool $monitored,
        public bool $hasFile,
        public string $path,
        public int $qualityProfileId,
        public ?int $runtime,
        public ?string $added,
        public ?string $inCinemas,
        public ?string $physicalRelease,
        public ?string $digitalRelease,
        public array $genres,
        public array $ratings,
        public ?array $movieFile,
        public array $images,
        public array $alternativeTitles,
        public int $sizeOnDisk,
        public bool $isAvailable,
        public ?string $folderName,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            title: $data['title'] ?? '',
            sortTitle: $data['sortTitle'] ?? '',
            originalTitle: $data['originalTitle'] ?? '',
            originalLanguage: $data['originalLanguage']['name'] ?? 'Unknown',
            year: $data['year'] ?? null,
            tmdbId: $data['tmdbId'] ?? null,
            imdbId: $data['imdbId'] ?? null,
            status: $data['status'] ?? 'unknown',
            overview: $data['overview'] ?? '',
            monitored: $data['monitored'] ?? false,
            hasFile: $data['hasFile'] ?? false,
            path: $data['path'] ?? '',
            qualityProfileId: $data['qualityProfileId'] ?? 0,
            runtime: $data['runtime'] ?? null,
            added: $data['added'] ?? null,
            inCinemas: $data['inCinemas'] ?? null,
            physicalRelease: $data['physicalRelease'] ?? null,
            digitalRelease: $data['digitalRelease'] ?? null,
            genres: $data['genres'] ?? [],
            ratings: $data['ratings'] ?? [],
            movieFile: $data['movieFile'] ?? null,
            images: $data['images'] ?? [],
            alternativeTitles: $data['alternativeTitles'] ?? [],
            sizeOnDisk: $data['sizeOnDisk'] ?? 0,
            isAvailable: $data['isAvailable'] ?? false,
            folderName: $data['folderName'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'sort_title'         => $this->sortTitle,
            'original_title'     => $this->originalTitle,
            'original_language'  => $this->originalLanguage,
            'year'               => $this->year,
            'tmdb_id'            => $this->tmdbId,
            'imdb_id'            => $this->imdbId,
            'status'             => $this->status,
            'overview'           => $this->overview,
            'monitored'          => $this->monitored,
            'has_file'           => $this->hasFile,
            'path'               => $this->path,
            'quality_profile_id' => $this->qualityProfileId,
            'runtime'            => $this->runtime,
            'added'              => $this->added,
            'in_cinemas'         => $this->inCinemas,
            'physical_release'   => $this->physicalRelease,
            'digital_release'    => $this->digitalRelease,
            'genres'             => $this->genres,
            'ratings'            => $this->ratings,
            'movie_file'         => $this->movieFile,
            'images'             => $this->images,
            'alternative_titles' => $this->alternativeTitles,
            'size_on_disk'       => $this->sizeOnDisk,
            'is_available'       => $this->isAvailable,
            'folder_name'        => $this->folderName,
        ];
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function isDownloaded(): bool
    {
        return $this->hasFile;
    }

    public function isMonitored(): bool
    {
        return $this->monitored;
    }

    public function getSizeOnDiskGb(): float
    {
        return round($this->sizeOnDisk / 1024 / 1024 / 1024, 2);
    }
}
