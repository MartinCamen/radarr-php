<?php

namespace MartinCamen\Radarr\Tests\Unit\Data;

use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MovieTest extends TestCase
{
    #[Test]
    public function itCanBeCreatedFromArray(): void
    {
        $data = MovieFactory::make(1, ['title' => 'Test Movie']);
        $movie = Movie::fromArray($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(1, $movie->id);
        $this->assertEquals('Test Movie', $movie->title);
    }

    #[Test]
    public function itCanBeConvertedToArray(): void
    {
        $data = MovieFactory::make(1);
        $movie = Movie::fromArray($data);
        $array = $movie->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertArrayHasKey('title', $array);
    }

    #[Test]
    public function itCanCheckIfReleased(): void
    {
        $movie = Movie::fromArray(MovieFactory::make(1, ['status' => 'released']));
        $announced = Movie::fromArray(MovieFactory::make(2, ['status' => 'announced']));

        $this->assertTrue($movie->isReleased());
        $this->assertFalse($announced->isReleased());
    }

    #[Test]
    public function itCanCheckIfDownloaded(): void
    {
        $movie = Movie::fromArray(MovieFactory::makeDownloaded(1));
        $notDownloaded = Movie::fromArray(MovieFactory::make(2));

        $this->assertTrue($movie->isDownloaded());
        $this->assertFalse($notDownloaded->isDownloaded());
    }

    #[Test]
    public function itCanCheckIfMonitored(): void
    {
        $movie = Movie::fromArray(MovieFactory::make(1, ['monitored' => true]));
        $unmonitored = Movie::fromArray(MovieFactory::makeUnmonitored(2));

        $this->assertTrue($movie->isMonitored());
        $this->assertFalse($unmonitored->isMonitored());
    }

    #[Test]
    public function itCanCalculateSizeOnDiskInGb(): void
    {
        $movie = Movie::fromArray(MovieFactory::make(1, ['sizeOnDisk' => 4500000000]));

        $this->assertEquals(4.19, $movie->getSizeOnDiskGb());
    }

    #[Test]
    public function itHandlesMissingData(): void
    {
        $movie = Movie::fromArray([]);

        $this->assertEquals(0, $movie->id);
        $this->assertEquals('', $movie->title);
        $this->assertNull($movie->year);
        $this->assertFalse($movie->monitored);
    }
}
