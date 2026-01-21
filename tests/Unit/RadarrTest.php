<?php

namespace MartinCamen\Radarr\Tests\Unit;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Client\RadarrApiClientInterface;
use MartinCamen\Radarr\Radarr;
use MartinCamen\Radarr\Testing\RadarrApiFake;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RadarrTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsMovieActionsFromMoviesMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $movieActions = new MovieActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('movies')
            ->willReturn($movieActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->movies();

        $this->assertInstanceOf(MovieActions::class, $result);
        $this->assertSame($movieActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsDownloadActionsFromDownloadsMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $downloadActions = new DownloadActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('downloads')
            ->willReturn($downloadActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->downloads();

        $this->assertInstanceOf(DownloadActions::class, $result);
        $this->assertSame($downloadActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsSystemActionsFromSystemMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $systemActions = new SystemActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('system')
            ->willReturn($systemActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->system();

        $this->assertInstanceOf(SystemActions::class, $result);
        $this->assertSame($systemActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsCalendarActionsFromCalendarMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $calendarActions = new CalendarActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('calendar')
            ->willReturn($calendarActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->calendar();

        $this->assertInstanceOf(CalendarActions::class, $result);
        $this->assertSame($calendarActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsHistoryActionsFromHistoryMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $historyActions = new HistoryActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('history')
            ->willReturn($historyActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->history();

        $this->assertInstanceOf(HistoryActions::class, $result);
        $this->assertSame($historyActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsWantedActionsFromWantedMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $wantedActions = new WantedActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('wanted')
            ->willReturn($wantedActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->wanted();

        $this->assertInstanceOf(WantedActions::class, $result);
        $this->assertSame($wantedActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsCommandActionsFromCommandMethod(): void
    {
        $restClient = $this->createMock(RestClientInterface::class);
        $commandActions = new CommandActions($restClient);

        $apiClient = $this->createMock(RadarrApiClientInterface::class);
        $apiClient->expects($this->once())
            ->method('command')
            ->willReturn($commandActions);

        $radarr = new Radarr($apiClient);
        $result = $radarr->command();

        $this->assertInstanceOf(CommandActions::class, $result);
        $this->assertSame($commandActions, $result);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itReturnsApiClientFromApiMethod(): void
    {
        $apiClient = $this->createMock(RadarrApiClientInterface::class);

        $radarr = new Radarr($apiClient);
        $radarrApiClient = $radarr->api();

        $this->assertInstanceOf(RadarrApiClientInterface::class, $radarrApiClient);
        $this->assertSame($apiClient, $radarrApiClient);
    }

    #[Test]
    public function itCreatesInstanceWithStaticCreate(): void
    {
        $radarr = Radarr::create(
            host: 'localhost',
            port: 7878,
            apiKey: 'test-api-key',
        );

        $this->assertInstanceOf(Radarr::class, $radarr);
        $this->assertInstanceOf(RadarrApiClientInterface::class, $radarr->api());
    }

    #[Test]
    public function itWorksWithRadarrApiFake(): void
    {
        $radarrApiFake = new RadarrApiFake();
        $radarr = new Radarr($radarrApiFake);

        $this->assertInstanceOf(MovieActions::class, $radarr->movies());
        $this->assertInstanceOf(DownloadActions::class, $radarr->downloads());
        $this->assertInstanceOf(SystemActions::class, $radarr->system());
        $this->assertInstanceOf(CalendarActions::class, $radarr->calendar());
        $this->assertInstanceOf(HistoryActions::class, $radarr->history());
        $this->assertInstanceOf(WantedActions::class, $radarr->wanted());
        $this->assertInstanceOf(CommandActions::class, $radarr->command());
    }
}
