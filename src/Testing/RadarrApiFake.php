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
use MartinCamen\Radarr\RadarrInterface;

class RadarrApiFake extends BaseApiFake implements RadarrInterface
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
