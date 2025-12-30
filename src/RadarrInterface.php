<?php

declare(strict_types=1);

namespace MartinCamen\Radarr;

use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Actions\QueueActions;
use MartinCamen\Radarr\Actions\SystemActions;
use MartinCamen\Radarr\Actions\WantedActions;

interface RadarrInterface
{
    public function movie(): MovieActions;

    public function queue(): QueueActions;

    public function history(): HistoryActions;

    public function calendar(): CalendarActions;

    public function system(): SystemActions;

    public function command(): CommandActions;

    public function wanted(): WantedActions;
}
