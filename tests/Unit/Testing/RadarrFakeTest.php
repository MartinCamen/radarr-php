<?php

namespace MartinCamen\Radarr\Tests\Unit\Testing;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\RadarrInterface;
use MartinCamen\Radarr\Testing\RadarrApiFake;
use MartinCamen\Radarr\Testing\RadarrFake;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RadarrFakeTest extends TestCase
{
    #[Test]
    public function itImplementsRadarrInterface(): void
    {
        $radarrFake = new RadarrFake();

        $this->assertInstanceOf(RadarrInterface::class, $radarrFake);
    }

    #[Test]
    public function itReturnsMovieActionsFromMoviesMethod(): void
    {
        $radarrFake = new RadarrFake();

        $movieActions = $radarrFake->movies();

        $this->assertInstanceOf(MovieActions::class, $movieActions);
    }

    #[Test]
    public function itReturnsDownloadActionsFromDownloadsMethod(): void
    {
        $radarrFake = new RadarrFake();

        $downloadActions = $radarrFake->downloads();

        $this->assertInstanceOf(DownloadActions::class, $downloadActions);
    }

    #[Test]
    public function itReturnsSystemActionsFromSystemMethod(): void
    {
        $radarrFake = new RadarrFake();

        $systemActions = $radarrFake->system();

        $this->assertInstanceOf(SystemActions::class, $systemActions);
    }

    #[Test]
    public function itReturnsCalendarActionsFromCalendarMethod(): void
    {
        $radarrFake = new RadarrFake();

        $calendarActions = $radarrFake->calendar();

        $this->assertInstanceOf(CalendarActions::class, $calendarActions);
    }

    #[Test]
    public function itReturnsHistoryActionsFromHistoryMethod(): void
    {
        $radarrFake = new RadarrFake();

        $historyActions = $radarrFake->history();

        $this->assertInstanceOf(HistoryActions::class, $historyActions);
    }

    #[Test]
    public function itReturnsWantedActionsFromWantedMethod(): void
    {
        $radarrFake = new RadarrFake();

        $wantedActions = $radarrFake->wanted();

        $this->assertInstanceOf(WantedActions::class, $wantedActions);
    }

    #[Test]
    public function itReturnsCommandActionsFromCommandMethod(): void
    {
        $radarrFake = new RadarrFake();

        $commandActions = $radarrFake->command();

        $this->assertInstanceOf(CommandActions::class, $commandActions);
    }

    #[Test]
    public function itReturnsRadarrApiClientInterfaceFromApiMethod(): void
    {
        $radarrFake = new RadarrFake();

        $radarrApiClient = $radarrFake->api();

        $this->assertInstanceOf(RadarrApiClientInterface::class, $radarrApiClient);
        $this->assertInstanceOf(RadarrApiFake::class, $radarrApiClient);
    }

    #[Test]
    public function itRecordsMethodCalls(): void
    {
        $radarrFake = new RadarrFake();

        $radarrFake->movies();
        $radarrFake->downloads();
        $radarrFake->system();

        $radarrFake->assertCalled('movies');
        $radarrFake->assertCalled('downloads');
        $radarrFake->assertCalled('system');
    }
}
