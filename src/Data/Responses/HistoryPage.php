<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Data\Responses;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use MartinCamen\ArrCore\Data\Responses\PaginatedResponse;
use Traversable;

/**
 * @implements IteratorAggregate<int, HistoryRecord>
 */
final class HistoryPage extends PaginatedResponse implements Countable, IteratorAggregate
{
    /** @param array<int, HistoryRecord> $records */
    public function __construct(
        int $page,
        int $pageSize,
        int $totalRecords,
        private readonly array $records = [],
    ) {
        parent::__construct($page, $pageSize, $totalRecords);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            page: $data['page'] ?? 1,
            pageSize: $data['pageSize'] ?? 10,
            totalRecords: $data['totalRecords'] ?? 0,
            records: array_map(
                HistoryRecord::fromArray(...),
                $data['records'] ?? [],
            ),
        );
    }

    /** @return array<int, HistoryRecord> */
    public function records(): array
    {
        return $this->records;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** @return Traversable<int, HistoryRecord> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->records);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'page'          => $this->page,
            'page_size'     => $this->pageSize,
            'total_records' => $this->totalRecords,
            'records'       => array_map(
                static fn(HistoryRecord $record): array => $record->toArray(),
                $this->records,
            ),
        ];
    }
}
