<?php

namespace MartinCamen\Radarr\Tests\Unit\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\ArrCore\Data\Enums\SortDirection;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\ArrCore\Data\Responses\DownloadStatus;
use MartinCamen\Radarr\Actions\DownloadActions;
use MartinCamen\Radarr\Data\Responses\Download;
use MartinCamen\Radarr\Data\Responses\DownloadPage;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DownloadActionsTest extends TestCase
{
    #[Test]
    public function itCanGetPaginatedDownloads(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::All, [
                'page'     => 1,
                'pageSize' => 50,
            ])
            ->willReturn(DownloadFactory::makePaginatedResponse(3));

        $downloadActions = new DownloadActions($client);
        $downloadPage = $downloadActions->all();

        $this->assertInstanceOf(DownloadPage::class, $downloadPage);
        $this->assertCount(3, $downloadPage);
    }

    #[Test]
    public function itCanGetDownloadsWithSorting(): void
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

        $downloadActions = new DownloadActions($client);
        $downloadPage = $downloadActions->all(
            new PaginationOptions(pageSize: 10),
            SortOptions::by('timeleft', SortDirection::Ascending),
        );

        $this->assertInstanceOf(DownloadPage::class, $downloadPage);
    }

    #[Test]
    public function itCanGetDownloadById(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::ById, ['id' => 123])
            ->willReturn(DownloadFactory::make(123));

        $downloadActions = new DownloadActions($client);
        $download = $downloadActions->find(123);

        $this->assertInstanceOf(Download::class, $download);
        $this->assertEquals(123, $download->id);
    }

    #[Test]
    public function itCanGetDownloadStatus(): void
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

        $downloadActions = new DownloadActions($client);
        $status = $downloadActions->status();

        $this->assertInstanceOf(DownloadStatus::class, $status);
        $this->assertEquals(5, $status->totalCount);
        $this->assertFalse($status->hasIssues());
    }

    #[Test]
    public function itCanDeleteDownload(): void
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

        $downloadActions = new DownloadActions($client);
        $downloadActions->delete(123, blocklist: true);

        // No exception means success
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itCanBulkDeleteDownloads(): void
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

        $downloadActions = new DownloadActions($client);
        $downloadActions->bulkDelete([1, 2, 3]);

        // No exception means success
        $this->addToAssertionCount(1);
    }
}
