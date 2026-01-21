<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Data\Responses;

use MartinCamen\ArrCore\Data\Responses\PaginatedResponse;

/** @extends PaginatedResponse<Download> */
final class DownloadPage extends PaginatedResponse
{
    /** @param array<int, Download> $records */
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
                Download::fromArray(...),
                $data['records'] ?? [],
            ),
        );
    }

    /** @return array<int, Download> */
    public function all(): array
    {
        return $this->records;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'page'         => $this->page(),
            'pageSize'     => $this->pageSize(),
            'totalRecords' => $this->total(),
            'records'      => array_map(
                static fn(Download $download): array => $download->toArray(),
                $this->records,
            ),
        ];
    }
}
