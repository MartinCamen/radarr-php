<?php

namespace MartinCamen\Radarr\Data\Options;

final readonly class QueueOptions implements RequestOptions
{
    use BuildsRequestParams;

    public function __construct(
        public ?bool $includeUnknownMovieItems = null,
        public ?bool $includeMovie = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        $this->addIfNotNull($params, 'includeUnknownMovieItems', $this->includeUnknownMovieItems);
        $this->addIfNotNull($params, 'includeMovie', $this->includeMovie);

        return $params;
    }

    public static function default(): self
    {
        return new self();
    }

    public function withIncludeUnknownMovieItems(bool $includeUnknownMovieItems): self
    {
        return new self($includeUnknownMovieItems, $this->includeMovie);
    }

    public function withIncludeMovie(bool $includeMovie): self
    {
        return new self($this->includeUnknownMovieItems, $includeMovie);
    }
}
