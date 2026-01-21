<?php

namespace MartinCamen\Radarr\Tests\Feature;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Enums\QueueEndpoint;
use MartinCamen\Radarr\Client\RadarrApiClient;
use MartinCamen\Radarr\Data\Enums\MovieEndpoint;
use MartinCamen\Radarr\Data\Responses\Download;
use MartinCamen\Radarr\Data\Responses\DownloadPage;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;
use MartinCamen\Radarr\Radarr;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RadarrIntegrationTest extends TestCase
{
    #[Test]
    public function itCanChainMoviesAll(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::All, [])
            ->willReturn(MovieFactory::makeMany(3));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movieCollection = $radarr->movies()->all();

        $this->assertInstanceOf(MovieCollection::class, $movieCollection);
        $this->assertCount(3, $movieCollection);
    }

    #[Test]
    public function itCanChainMoviesFind(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::ById, ['id' => 123])
            ->willReturn(MovieFactory::make(123));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movie = $radarr->movies()->find(123);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(123, $movie->id);
    }

    #[Test]
    public function itCanChainMoviesSearch(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::Lookup, ['term' => 'inception'])
            ->willReturn(MovieFactory::makeMany(5));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movieCollection = $radarr->movies()->search('inception');

        $this->assertInstanceOf(MovieCollection::class, $movieCollection);
        $this->assertCount(5, $movieCollection);
    }

    #[Test]
    public function itCanChainMoviesSearchByTmdb(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::LookupTmdb, ['tmdbId' => 27205])
            ->willReturn(MovieFactory::make(1, ['tmdbId' => 27205, 'title' => 'Inception']));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movie = $radarr->movies()->searchByTmdb(27205);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(27205, $movie->tmdbId);
    }

    #[Test]
    public function itCanChainMoviesSearchByImdb(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::LookupImdb, ['imdbId' => 'tt1375666'])
            ->willReturn(MovieFactory::make(1, ['imdbId' => 'tt1375666']));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movie = $radarr->movies()->searchByImdb('tt1375666');

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('tt1375666', $movie->imdbId);
    }

    #[Test]
    public function itCanChainMoviesAdd(): void
    {
        $movieData = [
            'title'            => 'Test Movie',
            'tmdbId'           => 12345,
            'qualityProfileId' => 1,
            'path'             => '/movies/Test Movie (2024)',
        ];

        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->with(MovieEndpoint::All, $movieData)
            ->willReturn(MovieFactory::make(1, $movieData));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movie = $radarr->movies()->add($movieData);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('Test Movie', $movie->title);
    }

    #[Test]
    public function itCanChainMoviesUpdate(): void
    {
        $movieData = ['monitored' => false];

        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('put')
            ->with(MovieEndpoint::ById, array_merge(['id' => 123], $movieData))
            ->willReturn(MovieFactory::make(123, ['monitored' => false]));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $movie = $radarr->movies()->update(123, $movieData);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertFalse($movie->monitored);
    }

    #[Test]
    public function itCanChainMoviesDelete(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('delete')
            ->with(MovieEndpoint::ById, [
                'id'                 => 123,
                'deleteFiles'        => true,
                'addImportExclusion' => false,
            ])
            ->willReturn(null);

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $radarr->movies()->delete(123, deleteFiles: true);

        // No exception means success
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itCanChainDownloadsAll(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::All, [
                'page'     => 1,
                'pageSize' => 50,
            ])
            ->willReturn(DownloadFactory::makePaginatedResponse(3));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $downloadPage = $radarr->downloads()->all();

        $this->assertInstanceOf(DownloadPage::class, $downloadPage);
        $this->assertCount(3, $downloadPage);
    }

    #[Test]
    public function itCanChainDownloadsFind(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('get')
            ->with(QueueEndpoint::ById, ['id' => 123])
            ->willReturn(DownloadFactory::make(123));

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $download = $radarr->downloads()->find(123);

        $this->assertInstanceOf(Download::class, $download);
        $this->assertEquals(123, $download->id);
    }

    #[Test]
    public function itCanChainDownloadsStatus(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
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

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $status = $radarr->downloads()->status();

        $this->assertEquals(5, $status->totalCount);
        $this->assertFalse($status->hasIssues());
    }

    #[Test]
    public function itCanChainDownloadsDelete(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('delete')
            ->with(QueueEndpoint::ById, [
                'id'               => 123,
                'removeFromClient' => true,
                'blocklist'        => true,
                'skipRedownload'   => false,
                'changeCategory'   => false,
            ])
            ->willReturn(null);

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $radarr->downloads()->delete(123, blocklist: true);

        // No exception means success
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itCanChainDownloadsBulkDelete(): void
    {
        $mockClient = $this->createMock(RestClientInterface::class);
        $mockClient->expects($this->once())
            ->method('delete')
            ->with(QueueEndpoint::Bulk, [
                'ids'              => [1, 2, 3],
                'removeFromClient' => true,
                'blocklist'        => false,
                'skipRedownload'   => false,
                'changeCategory'   => false,
            ])
            ->willReturn(null);

        $radarrApiClient = new RadarrApiClient(restClient: $mockClient);
        $radarr = new Radarr($radarrApiClient);

        $radarr->downloads()->bulkDelete([1, 2, 3]);

        // No exception means success
        $this->addToAssertionCount(1);
    }
}
