<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Testing;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Testing\BaseApiFake;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;

/**
 * Fake implementation for the low-level Radarr API client.
 *
 * Use this when you need to test code that interacts directly
 * with the API client layer.
 *
 * @internal
 */
class RadarrApiFake extends BaseApiFake implements RadarrApiClientInterface
{
    public function movies(): MovieActions
    {
        return new MovieActions($this->getFakeClient());
    }

    public function downloads(): DownloadActions
    {
        return new DownloadActions($this->getFakeClient());
    }

    public function history(): HistoryActions
    {
        return new HistoryActions($this->getFakeClient());
    }

    public function calendar(): CalendarActions
    {
        return new CalendarActions($this->getFakeClient());
    }

    public function system(): SystemActions
    {
        return new SystemActions($this->getFakeClient());
    }

    public function command(): CommandActions
    {
        return new CommandActions($this->getFakeClient());
    }

    public function wanted(): WantedActions
    {
        return new WantedActions($this->getFakeClient());
    }
}
