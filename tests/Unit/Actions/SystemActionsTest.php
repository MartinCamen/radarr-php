<?php

namespace MartinCamen\Radarr\Tests\Unit\Actions;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\SystemEndpoint;
use MartinCamen\ArrCore\Data\Responses\DiskSpaceCollection;
use MartinCamen\ArrCore\Domain\System\DownloadServiceSystemSummary;
use MartinCamen\ArrCore\Domain\System\HealthCheckCollection;
use MartinCamen\Radarr\Testing\Factories\SystemStatusFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SystemActionsTest extends TestCase
{
    #[Test]
    public function itCanGetSystemStatus(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::Status)
            ->willReturn(SystemStatusFactory::make());

        $systemActions = new SystemActions($client);
        $status = $systemActions->status();

        $this->assertInstanceOf(DownloadServiceSystemSummary::class, $status);
        $this->assertEquals('Radarr', $status->appName);
        $this->assertEquals('5.0.0.0000', $status->version);
    }

    #[Test]
    public function itCanGetHealthChecks(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::Health)
            ->willReturn([
                ['source' => 'IndexerStatusCheck', 'type' => 'warning', 'message' => 'Indexer unavailable', 'wikiUrl' => ''],
            ]);

        $systemActions = new SystemActions($client);
        $health = $systemActions->health();

        $this->assertInstanceOf(HealthCheckCollection::class, $health);
        $this->assertCount(1, $health);
        $this->assertTrue($health->hasWarnings());
    }

    #[Test]
    public function itCanGetDiskSpace(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::DiskSpace)
            ->willReturn([
                ['path' => '/movies', 'label' => 'Movies', 'freeSpace' => 500000000000, 'totalSpace' => 1000000000000],
            ]);

        $systemActions = new SystemActions($client);
        $diskSpace = $systemActions->diskSpace();

        $this->assertInstanceOf(DiskSpaceCollection::class, $diskSpace);
        $this->assertCount(1, $diskSpace);
        $this->assertEquals(500000000000, $diskSpace->totalFreeSpace()->toBytes());
    }

    #[Test]
    public function itCanGetScheduledTasks(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::Task)
            ->willReturn([
                ['id' => 1, 'name' => 'RssSync', 'taskName' => 'RssSync'],
            ]);

        $systemActions = new SystemActions($client);
        $tasks = $systemActions->tasks();

        $this->assertCount(1, $tasks);
    }

    #[Test]
    public function itCanGetSpecificTask(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::TaskById, ['id' => 1])
            ->willReturn(['id' => 1, 'name' => 'RssSync']);

        $systemActions = new SystemActions($client);
        $task = $systemActions->task(1);

        $this->assertEquals(1, $task['id']);
    }

    #[Test]
    public function itCanGetBackups(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(SystemEndpoint::Backup)
            ->willReturn([
                ['id' => 1, 'name' => 'radarr_backup_2024.01.01.zip', 'type' => 'scheduled'],
            ]);

        $systemActions = new SystemActions($client);
        $backups = $systemActions->backups();

        $this->assertCount(1, $backups);
    }
}
