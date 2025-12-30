<?php

namespace MartinCamen\Radarr\Data\Responses;

final readonly class HistoryRecord
{
    /**
     * @param array<string, mixed>|null $quality
     * @param array<string, mixed>|null $data
     * @param array<string, mixed>|null $movie
     */
    public function __construct(
        public int $id,
        public ?int $movieId,
        public string $sourceTitle,
        public string $eventType,
        public ?array $quality,
        public ?string $date,
        public string $downloadId,
        public ?array $data,
        public ?array $movie,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            movieId: $data['movieId'] ?? null,
            sourceTitle: $data['sourceTitle'] ?? '',
            eventType: $data['eventType'] ?? 'unknown',
            quality: $data['quality'] ?? null,
            date: $data['date'] ?? null,
            downloadId: $data['downloadId'] ?? '',
            data: $data['data'] ?? null,
            movie: $data['movie'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'movie_id'     => $this->movieId,
            'source_title' => $this->sourceTitle,
            'event_type'   => $this->eventType,
            'quality'      => $this->quality,
            'date'         => $this->date,
            'download_id'  => $this->downloadId,
            'data'         => $this->data,
            'movie'        => $this->movie,
        ];
    }

    public function isGrabbed(): bool
    {
        return $this->eventType === 'grabbed';
    }

    public function isImported(): bool
    {
        return $this->eventType === 'downloadFolderImported'
            || $this->eventType === 'movieFolderImported';
    }

    public function isFailed(): bool
    {
        return $this->eventType === 'downloadFailed';
    }

    public function isDeleted(): bool
    {
        return $this->eventType === 'movieFileDeleted';
    }
}
