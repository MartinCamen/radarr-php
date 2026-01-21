<?php

namespace MartinCamen\Radarr\Tests\Unit\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Data\Enums\MovieEndpoint;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MovieActionsTest extends TestCase
{
    #[Test]
    public function itCanGetAllMovies(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::All, [])
            ->willReturn(MovieFactory::makeMany(3));

        $movieActions = new MovieActions($client);
        $movieCollection = $movieActions->all();

        $this->assertInstanceOf(MovieCollection::class, $movieCollection);
        $this->assertCount(3, $movieCollection);
    }

    #[Test]
    public function itCanGetMovieById(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::ById, ['id' => 123])
            ->willReturn(MovieFactory::make(123));

        $movieActions = new MovieActions($client);
        $movie = $movieActions->find(123);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(123, $movie->id);
    }

    #[Test]
    public function itCanLookupMoviesByTerm(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::Lookup, ['term' => 'inception'])
            ->willReturn(MovieFactory::makeMany(5));

        $movieActions = new MovieActions($client);
        $movieCollection = $movieActions->search('inception');

        $this->assertInstanceOf(MovieCollection::class, $movieCollection);
        $this->assertCount(5, $movieCollection);
    }

    #[Test]
    public function itCanLookupMovieByTmdbId(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::LookupTmdb, ['tmdbId' => 27205])
            ->willReturn(MovieFactory::make(1, ['tmdbId' => 27205, 'title' => 'Inception']));

        $movieActions = new MovieActions($client);
        $movie = $movieActions->searchByTmdb(27205);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(27205, $movie->tmdbId);
    }

    #[Test]
    public function itCanLookupMovieByImdbId(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('get')
            ->with(MovieEndpoint::LookupImdb, ['imdbId' => 'tt1375666'])
            ->willReturn(MovieFactory::make(1, ['imdbId' => 'tt1375666']));

        $movieActions = new MovieActions($client);
        $movie = $movieActions->searchByImdb('tt1375666');

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('tt1375666', $movie->imdbId);
    }

    #[Test]
    public function itCanAddMovie(): void
    {
        $movieData = [
            'title'            => 'Test Movie',
            'tmdbId'           => 12345,
            'qualityProfileId' => 1,
            'path'             => '/movies/Test Movie (2024)',
        ];

        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('post')
            ->with(MovieEndpoint::All, $movieData)
            ->willReturn(MovieFactory::make(1, $movieData));

        $movieActions = new MovieActions($client);
        $movie = $movieActions->add($movieData);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals('Test Movie', $movie->title);
    }

    #[Test]
    public function itCanUpdateMovie(): void
    {
        $movieData = ['monitored' => false];

        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('put')
            ->with(MovieEndpoint::ById, array_merge(['id' => 123], $movieData))
            ->willReturn(MovieFactory::make(123, ['monitored' => false]));

        $movieActions = new MovieActions($client);
        $movie = $movieActions->update(123, $movieData);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertFalse($movie->monitored);
    }

    #[Test]
    public function itCanDeleteMovie(): void
    {
        $client = $this->createMock(RestClientInterface::class);
        $client->expects($this->once())
            ->method('delete')
            ->with(MovieEndpoint::ById, [
                'id'                 => 123,
                'deleteFiles'        => true,
                'addImportExclusion' => false,
            ])
            ->willReturn(null);

        $movieActions = new MovieActions($client);
        $movieActions->delete(123, deleteFiles: true);

        // No exception means success
        $this->addToAssertionCount(1);
    }
}
