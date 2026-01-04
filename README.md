# Radarr PHP SDK

A PHP SDK for the Radarr REST API v3.

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
$downloads = $radarr->downloads();

// Get all movies
$movies = $radarr->movies();

// Get system status
$status = $radarr->system()->status();
```

### Laravel Integration

For Laravel integration, use the [laravel-radarr](https://github.com/martincamen/laravel-radarr) package which provides facades, service provider, and configuration management.

## Usage

### Downloads (Queue)

Get active downloads using the unified `downloads()` method, which returns Core domain models compatible with other *arr services:

```php
use MartinCamen\Radarr\Radarr;

$radarr = Radarr::create('localhost', 7878, 'your-api-key');

// Get all active downloads
$downloads = $radarr->downloads();

foreach ($downloads as $item) {
    echo $item->name;
    echo $item->progress->percentage() . '%';
    echo $item->status->value;
    echo $item->size->formatted();
}

// Filter downloads by status
$active = $downloads->active();
$completed = $downloads->completed();
$failed = $downloads->failed();

// Get total size and progress
echo $downloads->totalSize()->formatted();
echo $downloads->totalProgress()->percentage() . '%';
```

### Movies

```php
// Get all movies
$movies = $radarr->movies();

foreach ($movies as $movie) {
    echo $movie->title;
    echo $movie->year;
    echo $movie->status->value;
    echo $movie->monitored ? 'Monitored' : 'Not monitored';
}

// Get a specific movie by ID
$movie = $radarr->movie(1);
echo $movie->title;
echo $movie->overview;
```

### System Status

```php
echo $radarr->system()->status()->version;

foreach ($radarr->system()->health()->warnings() as $warning) {
    echo $warning->type . ': ' . $warning->message;
}
```

### System Summary

```php
$summary = $radarr->systemSummary();

echo $summary->version;
echo $summary->isHealthy ? 'Healthy' : 'Issues detected';

foreach ($summary->healthIssues as $issue) {
    echo $issue->type . ': ' . $issue->message;
}
```

### Calendar

Access upcoming movie releases:

```php
use MartinCamen\Radarr\Data\Options\CalendarOptions;

// Get upcoming movies (defaults to today to today + 2 days)
$calendar = $radarr->calendar()->get();

// Get movies within a specific date range
$options = CalendarOptions::make()
    ->withDateRange(
        new DateTime('2024-01-01'),
        new DateTime('2024-01-31'),
    );

$movies = $radarr->calendar()->get($options);

// Include unmonitored movies and filter by tags
$options = CalendarOptions::make()
    ->withUnmonitored(true)
    ->withTags([1, 2]);

$movies = $radarr->calendar()->get($options);
```

### History

Access download history:

```php
use MartinCamen\Radarr\Data\Enums\HistoryEventType;
use MartinCamen\Radarr\Data\Options\HistoryOptions;
use MartinCamen\ArrCore\Data\Options\PaginationOptions;
use MartinCamen\ArrCore\Data\Options\SortOptions;

// Get paginated history with defaults
$history = $radarr->history()->all();

// Get history with custom pagination and sorting
$pagination = new PaginationOptions(page: 1, pageSize: 50);
$sort = SortOptions::by('date')->descending();
$history = $radarr->history()->all($pagination, $sort);

// Filter by event type
$filters = HistoryOptions::make()
    ->withEventType(HistoryEventType::Grabbed)
    ->withIncludeMovie(true);
$history = $radarr->history()->all(null, null, $filters);

// Get history for a specific movie
$records = $radarr->history()->forMovie(1);
```

### Wanted (Missing & Cutoff)

Access missing movies and quality cutoff:

```php
use MartinCamen\ArrCore\Data\Options\WantedOptions;

// Get paginated missing movies
$missing = $radarr->wanted()->missing();

// Filter to only monitored movies
$filters = WantedOptions::make()->onlyMonitored();
$missing = $radarr->wanted()->missing(null, null, $filters);

// Get ALL missing movies (automatically handles pagination)
$allMissing = $radarr->wanted()->allMissing();

// Get movies below quality cutoff
$cutoff = $radarr->wanted()->cutoff();
```

### Commands

Execute Radarr commands:

```php
use MartinCamen\ArrCore\Data\Enums\CommandName;

// Get all commands
$commands = $radarr->command()->all();

// Execute a refresh monitored downloads command
$command = $radarr->command()->execute(CommandName::RefreshMonitoredDownloads);

// Execute a movie search
$command = $radarr->command()->execute(
    CommandName::MoviesSearch,
    ['movieIds' => [1, 2, 3]],
);
```

### Advanced: Raw API Access

For operations not yet exposed through the SDK, use the `api()` method to access the low-level API client:

```php
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
$radarr->api()->movie()->add($movieData);

// Update a movie
$radarr->api()->movie()->update(1, $movieData);

// Delete a movie
$radarr->api()->movie()->delete(1, deleteFiles: true);

// Search for movies
$results = $radarr->api()->movie()->lookup('Inception');

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

        $movies = $fake->movies();

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

        $downloads = $fake->downloads();

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
RadarrApiClient (Internal API Client)
  ↓
HTTP Client
```

- **`Radarr`**: The public interface with unified terminology (`downloads()`, `movies()`) returning Core domain models
- **`RadarrApiClient`**: Internal client using Radarr's native API terminology (`queue()`, `movie()`)
- **Core Domain Models**: Shared types from `php-arr-core` for cross-service compatibility

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
