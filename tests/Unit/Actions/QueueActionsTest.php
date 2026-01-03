<?php

namespace MartinCamen\Radarr\Tests\Unit\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\ArrCore\Data\Enums\SortDirection;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\ArrCore\Data\Responses\QueueStatus;
use MartinCamen\Radarr\Actions\QueueActions;
use MartinCamen\Radarr\Data\Responses\QueuePage;
use MartinCamen\Radarr\Data\Responses\QueueRecord;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueueActionsTest extends TestCase
{
    #[Test]
    public function itCanGetPaginatedQueue(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::All, [
                'page'     => 1,
                'pageSize' => 50,
            ])
            ->willReturn(DownloadFactory::makePaginatedResponse(3));

        $queueActions = new QueueActions($client);
        $queue = $queueActions->all();

        $this->assertInstanceOf(QueuePage::class, $queue);
        $this->assertCount(3, $queue);
    }

    #[Test]
    public function itCanGetQueueWithSorting(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::All, [
                'page'          => 1,
                'pageSize'      => 10,
                'sortKey'       => 'timeleft',
                'sortDirection' => 'ascending',
            ])
            ->willReturn(DownloadFactory::makePaginatedResponse(2, pageSize: 10));

        $queueActions = new QueueActions($client);
        $queue = $queueActions->all(
            new PaginationOptions(pageSize: 10),
            SortOptions::by('timeleft', SortDirection::Ascending),
        );

        $this->assertInstanceOf(QueuePage::class, $queue);
    }

    #[Test]
    public function itCanGetQueueRecordById(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::ById, ['id' => 123])
            ->willReturn(DownloadFactory::make(123));

        $queueActions = new QueueActions($client);
        $record = $queueActions->find(123);

        $this->assertInstanceOf(QueueRecord::class, $record);
        $this->assertEquals(123, $record->id);
    }

    #[Test]
    public function itCanGetQueueStatus(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::Status)
            ->willReturn([
                'totalCount'      => 5,
                'count'           => 5,
                'unknownCount'    => 0,
                'errors'          => false,
                'warnings'        => false,
                'unknownErrors'   => false,
                'unknownWarnings' => false,
            ]);

        $queueActions = new QueueActions($client);
        $status = $queueActions->status();

        $this->assertInstanceOf(QueueStatus::class, $status);
        $this->assertEquals(5, $status->totalCount);
        $this->assertFalse($status->hasIssues());
    }

    #[Test]
    public function itCanDeleteQueueItem(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('delete')
            ->with(QueueEndpoint::ById, [
                'id'               => 123,
                'removeFromClient' => true,
                'blocklist'        => true,
                'skipRedownload'   => false,
                'changeCategory'   => false,
            ])
            ->willReturn(null);

        $queueActions = new QueueActions($client);
        $queueActions->delete(123, blocklist: true);

        $this->assertTrue(true);
    }

    #[Test]
    public function itCanBulkDeleteQueueItems(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('delete')
            ->with(QueueEndpoint::Bulk, [
                'ids'              => [1, 2, 3],
                'removeFromClient' => true,
                'blocklist'        => false,
                'skipRedownload'   => false,
                'changeCategory'   => false,
            ])
            ->willReturn(null);

        $queueActions = new QueueActions($client);
        $queueActions->bulkDelete([1, 2, 3]);

        $this->assertTrue(true);
    }
}
