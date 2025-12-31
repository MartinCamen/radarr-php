<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Testing;

use MartinCamen\ArrCore\Testing\BaseApiFake;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Actions\QueueActions;
use MartinCamen\Radarr\Actions\SystemActions;
use MartinCamen\Radarr\Actions\WantedActions;
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
    public function movie(): MovieActions
    {
        return new MovieActions($this->getFakeClient());
    }

    public function queue(): QueueActions
    {
        return new QueueActions($this->getFakeClient());
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
