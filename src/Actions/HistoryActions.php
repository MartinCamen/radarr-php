<?php

namespace MartinCamen\Radarr\Actions;

use DateTimeInterface;
use MartinCamen\ArrCore\Actions\HistoryActions as CoreHistoryActions;
use MartinCamen\ArrCore\Data\Enums\HistoryEndpoint;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Options\HistoryOptions;
use MartinCamen\Radarr\Data\Responses\HistoryPage;
use MartinCamen\Radarr\Data\Responses\HistoryRecord;

/** @link https://radarr.video/docs/api/#/History */
final readonly class HistoryActions extends CoreHistoryActions
{
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
        $requestFilters = $filters?->toArray() ?? [];

        if (($eventType = $filters?->eventType) instanceof \MartinCamen\Radarr\Data\Enums\HistoryEventType) {
            $requestFilters['eventType'] = $eventType->numericValue();
        }

        return HistoryPage::fromArray(
            $this->getAll($pagination, $sort, $requestFilters),
        );
    }

    /**
     * Get history since a specific date.
     *
     * @return array<string, HistoryRecord>
     */
    public function since(
        DateTimeInterface $date,
        ?HistoryOptions $filters = null,
    ): array {
        return array_map(
            HistoryRecord::fromArray(...),
            $this->getAllSince($date, $filters),
        );
    }

    /**
     * Get history for a specific movie.
     *
     * @return array<int, HistoryRecord>
     *
     * @link https://radarr.video/docs/api/#/History/get_api_v3_history_movie
     */
    public function find(
        int $id,
        ?HistoryOptions $filters = null,
    ): array {
        $params = array_merge(
            ['movieId' => $id],
            $filters?->toArray() ?? [],
        );

        $result = $this->client->get(HistoryEndpoint::Movie, $params);

        return array_map(
            HistoryRecord::fromArray(...),
            $result ?? [],
        );
    }
}
