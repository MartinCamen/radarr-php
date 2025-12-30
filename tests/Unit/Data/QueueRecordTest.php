<?php

namespace MartinCamen\Radarr\Tests\Unit\Data;

use MartinCamen\Radarr\Data\Responses\QueueRecord;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueueRecordTest extends TestCase
{
    #[Test]
    public function itCanBeCreatedFromArray(): void
    {
        $data = DownloadFactory::make(1);
        $record = QueueRecord::fromArray($data);

        $this->assertInstanceOf(QueueRecord::class, $record);
        $this->assertEquals(1, $record->id);
    }

    #[Test]
    public function itCanBeConvertedToArray(): void
    {
        $data = DownloadFactory::make(1);
        $record = QueueRecord::fromArray($data);
        $array = $record->toArray();

        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
    }

    #[Test]
    public function itCanCalculateProgress(): void
    {
        $record = QueueRecord::fromArray(DownloadFactory::make(1, [
            'size'     => 1000,
            'sizeleft' => 250,
        ]));

        $this->assertEquals(75.0, $record->getProgress());
    }

    #[Test]
    public function itHandlesZeroSizeForProgress(): void
    {
        $record = QueueRecord::fromArray(DownloadFactory::make(1, [
            'size'     => 0,
            'sizeleft' => 0,
        ]));

        $this->assertEquals(0.0, $record->getProgress());
    }

    #[Test]
    public function itCanCalculateSizeInGb(): void
    {
        $record = QueueRecord::fromArray(DownloadFactory::make(1, [
            'size' => 4500000000,
        ]));

        $this->assertEquals(4.19, $record->getSizeGb());
    }

    #[Test]
    public function itCanCheckIfCompleted(): void
    {
        $completed = QueueRecord::fromArray(DownloadFactory::makeCompleted(1));
        $downloading = QueueRecord::fromArray(DownloadFactory::make(2));

        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($downloading->isCompleted());
    }

    #[Test]
    public function itCanCheckIfHasError(): void
    {
        $withError = QueueRecord::fromArray(DownloadFactory::makeWithError(1));
        $normal = QueueRecord::fromArray(DownloadFactory::make(2));

        $this->assertTrue($withError->hasError());
        $this->assertFalse($normal->hasError());
    }
}
