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
        ?PaginationOptions $paginationOptions = null,
        ?SortOptions $sortOptions = null,
        ?HistoryOptions $historyOptions = null,
    ): HistoryPage {
        $requestFilters = $historyOptions?->toArray() ?? [];

        if (($eventType = $historyOptions?->eventType) instanceof \MartinCamen\Radarr\Data\Enums\HistoryEventType) {
            $requestFilters['eventType'] = $eventType->numericValue();
        }

        return HistoryPage::fromArray(
            $this->getAll($paginationOptions, $sortOptions, $requestFilters),
        );
    }

    /**
     * Get history since a specific date.
     *
     * @return array<string, HistoryRecord>
     */
    public function since(
        DateTimeInterface $date,
        ?HistoryOptions $historyOptions = null,
    ): array {
        return array_map(
            HistoryRecord::fromArray(...),
            $this->getAllSince($date, $historyOptions),
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
        ?HistoryOptions $historyOptions = null,
    ): array {
        $params = array_merge(
            ['movieId' => $id],
            $historyOptions?->toArray() ?? [],
        );

        $result = $this->client->get(HistoryEndpoint::Movie, $params);

        return array_map(
            HistoryRecord::fromArray(...),
            $result ?? [],
        );
    }
}
