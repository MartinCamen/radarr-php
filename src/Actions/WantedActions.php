<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Data\Enums\WantedEndpoint;
use MartinCamen\Radarr\Data\Options\PaginationOptions;
use MartinCamen\Radarr\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Options\WantedOptions;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/** @link https://radarr.video/docs/api/#/Wanted */
final readonly class WantedActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get missing movies (monitored, not downloaded).
     *
     * @return array<string, mixed>
     *
     * @link https://radarr.video/docs/api/#/Wanted/get_api_v3_wanted_missing
     */
    public function missing(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?WantedOptions $filters = null,
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::default()->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(WantedEndpoint::Missing, $params);
    }

    /**
     * Get all missing movies.
     */
    public function allMissing(?WantedOptions $filters = null): MovieCollection
    {
        $movies = [];
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->missing(
                new PaginationOptions($page, $pageSize),
                null,
                $filters,
            );
            $records = $result['records'] ?? [];
            $movies = array_merge($movies, $records);
            $totalRecords = $result['totalRecords'] ?? 0;
            $page++;
        } while (count($movies) < $totalRecords);

        return MovieCollection::fromArray($movies);
    }

    /**
     * Get movies below quality cutoff.
     *
     * @return array<string, mixed>
     *
     * @link https://radarr.video/docs/api/#/Wanted/get_api_v3_wanted_cutoff
     */
    public function cutoff(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?WantedOptions $filters = null,
    ): array {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::default()->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        return $this->client->get(WantedEndpoint::Cutoff, $params);
    }

    /**
     * Get all movies below quality cutoff.
     */
    public function allCutoff(?WantedOptions $filters = null): MovieCollection
    {
        $movies = [];
        $page = 1;
        $pageSize = 100;

        do {
            $result = $this->cutoff(
                new PaginationOptions($page, $pageSize),
                null,
                $filters,
            );
            $records = $result['records'] ?? [];
            $movies = array_merge($movies, $records);
            $totalRecords = $result['totalRecords'] ?? 0;
            $page++;
        } while (count($movies) < $totalRecords);

        return MovieCollection::fromArray($movies);
    }
}
