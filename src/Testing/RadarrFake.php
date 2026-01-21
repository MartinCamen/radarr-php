<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Testing;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Testing\BaseFake;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\RadarrInterface;

/**
 * Fake implementation for testing.
 *
 * Provides the same interface as the Radarr SDK but allows
 * custom responses and tracks method calls for assertions.
 *
 * @example
 * ```php
 * $fake = new RadarrFake();
 *
 * $movies = $fake->movies()->all();
 * $movie = $fake->movies()->find(123);
 * $fake->assertCalled('movies');
 * ```
 */
final class RadarrFake extends BaseFake implements RadarrInterface
{
    private ?RadarrApiFake $radarrApiFake = null;

    /** Access movie functionality */
    public function movies(): MovieActions
    {
        $this->recordCall('movies', []);

        return $this->api()->movies();
    }

    /** Access download functionality (queue items) */
    public function downloads(): DownloadActions
    {
        $this->recordCall('downloads', []);

        return $this->api()->downloads();
    }

    /** Get system status */
    public function system(): SystemActions
    {
        $this->recordCall('system', []);

        return $this->api()->system();
    }

    /** Access calendar functionality */
    public function calendar(): CalendarActions
    {
        $this->recordCall('calendar', []);

        return $this->api()->calendar();
    }

    /** Access history functionality */
    public function history(): HistoryActions
    {
        $this->recordCall('history', []);

        return $this->api()->history();
    }

    /** Access wanted functionality */
    public function wanted(): WantedActions
    {
        $this->recordCall('wanted', []);

        return $this->api()->wanted();
    }

    /** Access command functionality */
    public function command(): CommandActions
    {
        $this->recordCall('command', []);

        return $this->api()->command();
    }

    /** Get the underlying API client fake for advanced operations */
    public function api(): RadarrApiClientInterface
    {
        return $this->radarrApiFake ??= new RadarrApiFake();
    }
}
