<?php

namespace MartinCamen\Radarr\Actions;

use DateTimeInterface;
use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Data\Enums\HistoryEndpoint;
use MartinCamen\Radarr\Data\Options\HistoryOptions;
use MartinCamen\Radarr\Data\Options\PaginationOptions;
use MartinCamen\Radarr\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Responses\HistoryPage;
use MartinCamen\Radarr\Data\Responses\HistoryRecord;

/** @link https://radarr.video/docs/api/#/History */
final readonly class HistoryActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get paginated history.
     *
     * @link https://radarr.video/docs/api/#/History/get_api_v3_history
     */
    public function all(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?HistoryOptions $filters = null,
    ): HistoryPage {
        $params = array_merge(
            $pagination?->toArray() ?? PaginationOptions::default()->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        $result = $this->client->get(HistoryEndpoint::All, $params);

        return HistoryPage::fromArray($result);
    }

    /**
     * Get history since a specific date.
     *
     * @return array<int, HistoryRecord>
     *
     * @link https://radarr.video/docs/api/#/History/get_api_v3_history_since
     */
    public function since(
        DateTimeInterface $date,
        ?HistoryOptions $filters = null,
    ): array {
        $params = array_merge(
            ['date' => $date->format('Y-m-d')],
            $filters?->toArray() ?? [],
        );

        $result = $this->client->get(HistoryEndpoint::Since, $params);

        return array_map(
            HistoryRecord::fromArray(...),
            $result ?? [],
        );
    }

    /**
     * Get history for a specific movie.
     *
     * @return array<int, HistoryRecord>
     *
     * @link https://radarr.video/docs/api/#/History/get_api_v3_history_movie
     */
    public function forMovie(
        int $movieId,
        ?HistoryOptions $filters = null,
    ): array {
        $params = array_merge(
            ['movieId' => $movieId],
            $filters?->toArray() ?? [],
        );

        $result = $this->client->get(HistoryEndpoint::Movie, $params);

        return array_map(
            HistoryRecord::fromArray(...),
            $result ?? [],
        );
    }

    /**
     * Mark a history item as failed.
     *
     * @link https://radarr.video/docs/api/#/History/post_api_v3_history_failed__id_
     */
    public function markFailed(int $id): void
    {
        $this->client->post(HistoryEndpoint::Failed, ['id' => $id]);
    }
}
