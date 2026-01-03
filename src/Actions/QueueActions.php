<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Actions\QueueActions as CoreQueueActions;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Options\QueueOptions;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Data\Responses\QueueRecord;

/** @link https://radarr.video/docs/api/#/Queue */
final readonly class QueueActions extends CoreQueueActions
{
    /** Get paginated queue */
    public function all(
        ?PaginationOptions $pagination = null,
        ?SortOptions $sort = null,
        ?QueueOptions $filters = null,
    ): QueuePage {
        return QueuePage::fromArray(
            $this->getAll($pagination, $sort, $filters),
        );
    }

    /** Get queue item by ID */
    public function find(int $id): QueueRecord
    {
        return QueueRecord::fromArray(
            $this->client->get(QueueEndpoint::ById, ['id' => $id]),
        );
    }
}
