<?php

namespace MartinCamen\Radarr\Data\Options;

use MartinCamen\ArrCore\Data\Options\BuildsRequestParams;
use MartinCamen\ArrCore\Data\Options\HistoryRequestOptions;
use MartinCamen\Radarr\Data\Enums\HistoryEventType;

final readonly class HistoryOptions implements HistoryRequestOptions
{
    use BuildsRequestParams;

    /**
     * @param array<int, int>|null $movieIds
     */
    public function __construct(
        public ?HistoryEventType $eventType = null,
        public ?bool $includeMovie = null,
        public ?array $movieIds = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        $this->addEnumIfNotNull($params, 'eventType', $this->eventType);
        $this->addIfNotNull($params, 'includeMovie', $this->includeMovie);
        $this->addArrayAsStringIfNotNull($params, 'movieIds', $this->movieIds);

        return $params;
    }

    public static function make(
        ?HistoryEventType $eventType = null,
        ?bool $includeMovie = null,
        ?array $movieIds = null,
    ): self {
        return new self(
            eventType: $eventType,
            includeMovie: $includeMovie,
            movieIds: $movieIds,
        );
    }

    public function withEventType(HistoryEventType $eventType): self
    {
        return new self($eventType, $this->includeMovie, $this->movieIds);
    }

    public function withIncludeMovie(bool $includeMovie): self
    {
        return new self($this->eventType, $includeMovie, $this->movieIds);
    }

    /** @param array<int, int> $movieIds */
    public function withMovieIds(array $movieIds): self
    {
        return new self($this->eventType, $this->includeMovie, $movieIds);
    }
}
