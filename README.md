# Radarr PHP SDK

> [!IMPORTANT]
> This project is still being developed and breaking changes might occur even between patch versions.
>
> The aim is to follow semantic versioning as soon as possible.

A PHP SDK for the Radarr REST API v3.

## Ecosystem

| Package                                                                 | Description                        |
|-------------------------------------------------------------------------|------------------------------------|
| [radarr-php](https://github.com/martincamen/radarr-php)                 | PHP SDK for Radarr                 |
| [sonarr-php](https://github.com/martincamen/sonarr-php)                 | PHP SDK for Sonarr                 |
| [jellyseerr-php](https://github.com/martincamen/jellyseerr-php)         | PHP SDK for Jellyseerr             |
| [laravel-radarr](https://github.com/martincamen/laravel-radarr)         | Laravel integration for Radarr     |
| [laravel-sonarr](https://github.com/martincamen/laravel-sonarr)         | Laravel integration for Sonarr     |
| [laravel-jellyseerr](https://github.com/martincamen/laravel-jellyseerr) | Laravel integration for Jellyseerr |

## Requirements

- PHP 8.3+

## Installation

```bash
composer require martincamen/radarr-php
```

## Quick Start

```php
use MartinCamen\Radarr\Radarr;

$radarr = Radarr::create(
    host: 'localhost',
    port: 7878,
    apiKey: 'your-api-key',
    useHttps: false,
);

// Get all downloads (queue items)
$downloads = $radarr->downloads()->all();

// Get all movies
$movies = $radarr->movies()->all();

// Get a specific movie
$movie = $radarr->movies()->find(1);

// Get system status
$status = $radarr->system()->status();
```

### Laravel Integration

For Laravel integration, use the [laravel-radarr](https://github.com/martincamen/laravel-radarr) package which provides facades, service provider, and configuration management.

## Usage

### Downloads (Queue)

Get active downloads using the `downloads()` action:

```php
use MartinCamen\Radarr\Radarr;

$radarr = Radarr::create('localhost', 7878, 'your-api-key');

// Get all active downloads (paginated)
$downloadPage = $radarr->downloads()->all();

foreach ($downloadPage as $item) {
    echo $item->title;
    echo $item->status->value;
    echo $item->sizeLeft;
}

// Get a specific download by ID
$download = $radarr->downloads()->find(1);

// Get download status summary
$status = $radarr->downloads()->status();
echo "Total: {$status->totalCount}";
echo "Unknown: {$status->unknownCount}";

// Delete a download
$radarr->downloads()->delete(1);

// Bulk delete downloads
$radarr->downloads()->bulkDelete([1, 2, 3]);
```

### Movies

```php
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;
use MartinCamen\Radarr\Radarr;

// Get all movies
/** @var MovieCollection $movies */
$movies = $radarr->movies()->all();

foreach ($movies as $movie) {
    echo $movie->title;
    echo $movie->year;
    echo $movie->status->value;
    echo $movie->monitored ? 'Monitored' : 'Not monitored';
}

// Get a specific movie by ID
/** @var Movie $movie */
$movie = $radarr->movies()->find(1);

echo $movie->title;
echo $movie->overview;

// Search for movies
$results = $radarr->movies()->search('Inception');

// Search by external IDs
$movie = $radarr->movies()->searchByTmdb(27205);
$movie = $radarr->movies()->searchByImdb('tt1375666');

// Add a new movie
$movie = $radarr->movies()->add([
    'title'            => 'Inception',
    'tmdbId'           => 27205,
    'qualityProfileId' => 1,
    'rootFolderPath'   => '/movies/',
]);

// Update a movie
$movie = $radarr->movies()->update(1, ['monitored' => false]);

// Delete a movie
$radarr->movies()->delete(1);
```

### System

```php
use MartinCamen\ArrCore\Actions\SystemActions;

/** @var SystemActions $system */
$system = $radarr->system();

// Get system status
$status = $system->status();
echo $status->version;
echo $status->osName;

// Get system health
$health = $system->health();
foreach ($health->warnings() as $warning) {
    echo $warning->type . ': ' . $warning->message;
}

// Get disk space
$diskSpace = $system->diskSpace();
foreach ($diskSpace as $disk) {
    echo $disk->path . ': ' . $disk->freeSpace;
}

// Get system tasks
$tasks = $system->tasks();
$task = $system->task(1);

// Get backups
$backups = $system->backups();
```

### Calendar

Access upcoming movie releases:

```php
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Data\Options\CalendarOptions;

/** @var CalendarActions $calendar */
$calendar = $radarr->calendar();

// Get upcoming movies (defaults to today to today + 2 days)
$calendar = $calendar->all();

// Get movies within a specific date range
$options = CalendarOptions::make()
    ->withDateRange(
        new DateTime('2024-01-01'),
        new DateTime('2024-01-31'),
    );

$movies = $calendar->all($options);

// Include unmonitored movies and filter by tags
$options = CalendarOptions::make()
    ->withUnmonitored(true)
    ->withTags([1, 2]);

$movies = $calendar->all($options);
```

### History

Access download history:

```php
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Data\Enums\HistoryEventType;
use MartinCamen\Radarr\Data\Options\HistoryOptions;

/** @var HistoryActions $history */
$history = $radarr->history();

/** @var HistoryPage $historyPage */
// Get paginated history with defaults
$historyPage = $history->all();

// Get history with custom pagination and sorting
$pagination = new PaginationOptions(page: 1, pageSize: 50);
$sort = SortOptions::by('date')->descending();
$history = $history->all($pagination, $sort);

// Filter by event type
$filters = HistoryOptions::make()
    ->withEventType(HistoryEventType::Grabbed)
    ->withIncludeMovie(true);
$history = $history->all(null, null, $filters);
```

### Wanted (Missing & Cutoff)

Access missing movies and quality cutoff:

```php
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Data\Options\WantedOptions;

/** @var WantedActions $wanted */
$wanted = $radarr->wanted();

// Get paginated missing movies
$missing = $wanted->missing();

// Filter to only monitored movies
$filters = WantedOptions::make()->onlyMonitored();
$missing = $wanted->missing(null, null, $filters);

// Get ALL missing movies (automatically handles pagination)
$allMissing = $wanted->allMissing();

// Get movies below quality cutoff
$cutoff = $wanted->cutoff();
```

### Commands

Execute Radarr commands:

```php
use MartinCamen\ArrCore\Data\Enums\CommandName;

/** @var CommandActions $commands */
$commands = $radarr->command();

// Get all commands
$commands = $commands->all();

// Execute a refresh monitored downloads command
$command = $commands->execute(CommandName::RefreshMonitoredDownloads);

// Execute a movie search
$command = $commands->execute(
    CommandName::MoviesSearch,
    ['movieIds' => [1, 2, 3]],
);
```

### Advanced: Raw API Access

For operations not yet exposed through the SDK, use the `api()` method to access the low-level API client:

```php
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;
use MartinCamen\Radarr\Data\Options\QueueOptions;
use MartinCamen\Radarr\Radarr;

// Add a new movie
$movieData = [
    'title'            => 'Inception',
    'qualityProfileId' => 1,
    'tmdbId'           => 27205,
    'year'             => 2010,
    'rootFolderPath'   => '/movies/',
    'monitored'        => true,
    'addOptions'       => [
        'searchForMovie' => true,
    ],
];

/** @var Radarr $radarr */
$radarr->api()->movie()->add($movieData);

// Update a movie
$radarr->api()->movie()->update(1, $movieData);

// Delete a movie
$radarr->api()->movie()->delete(1, deleteFiles: true);

// Search for movies
$results = $radarr->api()->movie()->search('Inception');

// Get queue with full options
$pagination = new PaginationOptions(page: 1, pageSize: 100);
$sort = SortOptions::by('timeleft')->ascending();
$filters = QueueOptions::make()->withIncludeMovie(true);
$queue = $radarr->api()->queue()->all($pagination, $sort, $filters);

// Delete from queue
$radarr->api()->queue()->delete(
    id: 1,
    removeFromClient: true,
    blocklist: false,
);

// Get disk space
$diskSpace = $radarr->api()->system()->diskSpace();
```

## Request Options

The SDK provides typed request option classes:

### Pagination Options

```php
use MartinCamen\ArrCore\Data\Options\PaginationOptions;

$options = PaginationOptions::make();
$options = new PaginationOptions(page: 2, pageSize: 50);
$options = PaginationOptions::make()->withPage(3)->withPageSize(100);
```

### Sort Options

```php
use MartinCamen\ArrCore\Data\Options\SortOptions;

$options = SortOptions::make(sortTitle: 'title');
$options = SortOptions::by('title')->ascending();
$options = SortOptions::by('date')->descending();
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use MartinCamen\ArrCore\Exceptions\AuthenticationException;
use MartinCamen\ArrCore\Exceptions\ConnectionException;
use MartinCamen\ArrCore\Exceptions\NotFoundException;
use MartinCamen\ArrCore\Exceptions\ValidationException;

try {
    $movie = $radarr->movie(999);
} catch (AuthenticationException $e) {
    // Invalid API key
} catch (NotFoundException $e) {
    // Movie not found
} catch (ConnectionException $e) {
    // Could not connect to server
} catch (ValidationException $e) {
    // Validation error
    print_r($e->getErrors());
}
```

## Testing

The SDK provides testing utilities for easy mocking:

```php
use PHPUnit\Framework\Attributes\Test;
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;
use MartinCamen\Radarr\Testing\RadarrFake;

class MyTest extends TestCase
{
    #[Test]
    public function itDisplaysMovies(): void
    {
        $fake = new RadarrFake([
            'movies' => MovieFactory::makeMany(5),
        ]);

        $movies = $fake->movies()->all();

        $this->assertCount(5, $movies);
        $fake->assertCalled('movies');
        $fake->assertCalledTimes('movies', 1);
    }

    #[Test]
    public function itDisplaysDownloads(): void
    {
        $fake = new RadarrFake([
            'downloads' => DownloadFactory::makeMany(3),
        ]);

        $downloads = $fake->downloads()->all();

        $this->assertCount(3, $downloads);
        $fake->assertCalled('downloads');
    }
}
```

### Using Factories

```php
use MartinCamen\Radarr\Testing\Factories\DownloadFactory;
use MartinCamen\Radarr\Testing\Factories\MovieFactory;

// Create movie data
$movie = MovieFactory::make(1);
$movies = MovieFactory::makeMany(5);
$movie = MovieFactory::make(123, [
    'title' => 'Inception',
    'year'  => 2010,
]);

// Create download data
$downloads = DownloadFactory::makeMany(3);
$completed = DownloadFactory::makeCompleted(1);
$withError = DownloadFactory::makeWithError(2);
```

## Architecture

The SDK follows a layered architecture:

```
Radarr (Public SDK)
  ↓
Action Classes (MovieActions, DownloadActions, etc.)
  ↓
Endpoint Classes (MovieEndpoint, QueueEndpoint, etc.)
  ↓
HTTP Client
```

- **`Radarr`**: The public entry point returning action classes
- **Action Classes**: Type-safe methods for each domain (`movies()->all()`, `downloads()->find(1)`)
- **Endpoint Classes**: Low-level API calls using Radarr's native terminology
- **Response Types**: Typed DTOs from the SDK (`Movie`, `DownloadPage`, etc.)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
