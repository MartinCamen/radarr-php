<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Client;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Actions\QueueActions;

/**
 * Interface for low-level Radarr API client.
 *
 * @internal This interface is for internal use. Use RadarrInterface for public API.
 */
interface RadarrApiClientInterface
{
    public function movie(): MovieActions;

    public function queue(): QueueActions;

    public function history(): HistoryActions;

    public function calendar(): CalendarActions;

    public function system(): SystemActions;

    public function command(): CommandActions;

    public function wanted(): WantedActions;
}
