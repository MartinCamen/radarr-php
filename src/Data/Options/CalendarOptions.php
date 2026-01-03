<?php

namespace MartinCamen\Radarr\Data\Options;

use DateTimeInterface;
use MartinCamen\ArrCore\Data\Options\BuildsRequestParams;
use MartinCamen\ArrCore\Data\Options\RequestOptions;

final readonly class CalendarOptions implements RequestOptions
{
    use BuildsRequestParams;

    /**
     * @param array<int, int>|null $tags
     */
    public function __construct(
        public ?DateTimeInterface $start = null,
        public ?DateTimeInterface $end = null,
        public ?bool $unmonitored = null,
        public ?array $tags = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $params = [];

        $this->addDateIfNotNull($params, 'start', $this->start);
        $this->addDateIfNotNull($params, 'end', $this->end);
        $this->addIfNotNull($params, 'unmonitored', $this->unmonitored);
        $this->addArrayAsStringIfNotNull($params, 'tags', $this->tags);

        return $params;
    }

    public static function default(): self
    {
        return new self();
    }

    public function withDateRange(?DateTimeInterface $start, ?DateTimeInterface $end): self
    {
        return new self($start, $end, $this->unmonitored, $this->tags);
    }

    public function withUnmonitored(bool $unmonitored): self
    {
        return new self($this->start, $this->end, $unmonitored, $this->tags);
    }

    /** @param  array<int, int>  $tags */
    public function withTags(array $tags): self
    {
        return new self($this->start, $this->end, $this->unmonitored, $tags);
    }
}
