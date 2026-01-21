<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Actions\DownloadActions as CoreDownloadActions;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Options\DownloadOptions;
use MartinCamen\Radarr\Data\Responses\Download;
use MartinCamen\Radarr\Data\Responses\DownloadPage;

/** @link https://radarr.video/docs/api/#/Queue */
final readonly class DownloadActions extends CoreDownloadActions
{
    /** Get paginated downloads */
    public function all(
        ?PaginationOptions $paginationOptions = null,
        ?SortOptions $sortOptions = null,
        ?DownloadOptions $downloadOptions = null,
    ): DownloadPage {
        return DownloadPage::fromArray(
            $this->getAll($paginationOptions, $sortOptions, $downloadOptions),
        );
    }

    /** Get download by ID */
    public function find(int $id): Download
    {
        return Download::fromArray(
            $this->client->get(QueueEndpoint::ById, ['id' => $id]),
        );
    }
}
