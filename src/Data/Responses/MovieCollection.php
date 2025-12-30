<?php

namespace MartinCamen\Radarr\Data\Responses;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Movie>
 */
final class MovieCollection implements Countable, IteratorAggregate
{
    /** @param array<int, Movie> $movies */
    public function __construct(private array $movies = []) {}

    /** @param array<int, array<string, mixed>> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            array_map(
                Movie::fromArray(...),
                $data,
            ),
        );
    }

    /** @return array<int, Movie> */
    public function all(): array
    {
        return $this->movies;
    }

    public function count(): int
    {
        return count($this->movies);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function first(): ?Movie
    {
        return $this->movies[0] ?? null;
    }

    public function last(): ?Movie
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->movies[$this->count() - 1];
    }

    public function get(int $index): ?Movie
    {
        return $this->movies[$index] ?? null;
    }

    /** @return Traversable<int, Movie> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->movies);
    }

    /** @return array<int, array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(
            fn(Movie $movie): array => $movie->toArray(),
            $this->movies,
        );
    }

    public function monitored(): self
    {
        return new self(
            array_values(array_filter(
                $this->movies,
                fn(Movie $movie): bool => $movie->isMonitored(),
            )),
        );
    }

    public function downloaded(): self
    {
        return new self(
            array_values(array_filter(
                $this->movies,
                fn(Movie $movie): bool => $movie->isDownloaded(),
            )),
        );
    }

    public function missing(): self
    {
        return new self(
            array_values(array_filter(
                $this->movies,
                fn(Movie $movie): bool => ! $movie->isDownloaded() && $movie->isMonitored(),
            )),
        );
    }
}
