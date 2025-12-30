<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Responses\QueueStatus;
use MartinCamen\Radarr\Data\Enums\QueueEndpoint;
use MartinCamen\Radarr\Data\Options\PaginationOptions;
use MartinCamen\Radarr\Data\Options\QueueOptions;
use MartinCamen\Radarr\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Data\Responses\QueueRecord;

/** @link https://radarr.video/docs/api/#/Queue */
final readonly class QueueActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get paginated queue.
     *
     * @link https://radarr.video/docs/api/#/Queue/get_api_v3_queue
     */
    public function all(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?QueueOptions $filters = null,
    ): QueuePage {
        $params = array_merge(
            $pagination?->toArray() ?? (new PaginationOptions(pageSize: 50))->toArray(),
            $sort?->toArray() ?? [],
            $filters?->toArray() ?? [],
        );

        $result = $this->client->get(QueueEndpoint::All, $params);

        return QueuePage::fromArray($result);
    }

    /**
     * Get queue item by ID.
     *
     * @link https://radarr.video/docs/api/#/Queue/get_api_v3_queue__id_
     */
    public function get(int $id): QueueRecord
    {
        $result = $this->client->get(QueueEndpoint::ById, ['id' => $id]);

        return QueueRecord::fromArray($result);
    }

    /**
     * Get queue status (counts, errors, warnings).
     *
     * @link https://radarr.video/docs/api/#/Queue/get_api_v3_queue_status
     */
    public function status(): QueueStatus
    {
        $result = $this->client->get(QueueEndpoint::Status);

        return QueueStatus::fromArray($result);
    }

    /**
     * Delete item from queue.
     *
     * @link https://radarr.video/docs/api/#/Queue/delete_api_v3_queue__id_
     */
    public function delete(
        int $id,
        bool $removeFromClient = true,
        bool $blocklist = false,
        bool $skipRedownload = false,
        bool $changeCategory = false,
    ): void {
        $this->client->delete(QueueEndpoint::ById, [
            'id'               => $id,
            'removeFromClient' => $removeFromClient,
            'blocklist'        => $blocklist,
            'skipRedownload'   => $skipRedownload,
            'changeCategory'   => $changeCategory,
        ]);
    }

    /**
     * Bulk delete items from queue.
     *
     * @param array<int, int> $ids
     *
     * @link https://radarr.video/docs/api/#/Queue/delete_api_v3_queue_bulk
     */
    public function bulkDelete(
        array $ids,
        bool $removeFromClient = true,
        bool $blocklist = false,
        bool $skipRedownload = false,
        bool $changeCategory = false,
    ): void {
        $this->client->delete(QueueEndpoint::Bulk, [
            'ids'              => $ids,
            'removeFromClient' => $removeFromClient,
            'blocklist'        => $blocklist,
            'skipRedownload'   => $skipRedownload,
            'changeCategory'   => $changeCategory,
        ]);
    }
}
