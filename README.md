# Radarr PHP SDK

A PHP SDK for the Radarr REST API v3.

## Requirements

- PHP 8.3+
- Laravel 10.0+, 11.0+ or 12.0+

## Installation

```bash
composer require martincamen/radarr-php
```

## Configuration

### Usage

```php
use MartinCamen\Radarr\Radarr;

$radarr = Radarr::make(
    host: 'localhost',
    port: 7878,
    apiKey: 'your-api-key',
    useHttps: false,
);

$movies = $radarr->movie()->all();
$systemStatus = $radarr->system()->status();
```

### Laravel Integration

For Laravel integration, use the [laravel-radarr](https://github.com/martincamen/laravel-radarr) package which provides facades, service provider, and configuration management.

## Usage

### Movie Management

```php
use MartinCamen\Radarr\Radarr;

$radarr = Radarr::make('localhost', 7878, 'your-api-key');

// Get all movies
$movies = $radarr->movie()->all();

// Get a specific movie by ID
$movie = $radarr->movie()->get(1);

// Search for movies by term (title, IMDb ID, or TMDb ID)
$results = $radarr->movie()->lookup('Inception');

// Lookup by TMDb ID
$movie = $radarr->movie()->lookupByTmdb(27205);

// Lookup by IMDb ID
$movie = $radarr->movie()->lookupByImdb('tt1375666');

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
$radarr->movie()->add($movieData);

// Update a movie
$radarr->movie()->update(1, $movieData);

// Delete a movie
$radarr->movie()->delete(1, deleteFiles: true, addImportExclusion: false);
```

### Calendar

```php
use MartinCamen\Radarr\Data\Options\CalendarOptions;

// Get upcoming movies (defaults to today to today + 2 days)
$calendar = $radarr->calendar()->get();

// Get movies within a specific date range
$options = CalendarOptions::default()
    ->withDateRange(
        new DateTime('2024-01-01'),
        new DateTime('2024-01-31'),
    );
$movies = $radarr->calendar()->get($options);

// Include unmonitored movies and filter by tags
$options = CalendarOptions::default()
    ->withUnmonitored(true)
    ->withTags([1, 2]);
$movies = $radarr->calendar()->get($options);

// Get calendar event by ID
$movie = $radarr->calendar()->getById(1);
```

### History

```php
use MartinCamen\Radarr\Data\Enums\HistoryEventType;
use MartinCamen\Radarr\Data\Options\{HistoryOptions, PaginationOptions, SortOptions};

// Get paginated history with defaults
$history = $radarr->history()->all();

// Get history with custom pagination and sorting
$pagination = new PaginationOptions(page: 1, pageSize: 50);
$sort = SortOptions::by('date')->descending();
$history = $radarr->history()->all($pagination, $sort);

// Filter by event type and include movie details
$filters = HistoryOptions::default()
    ->withEventType(HistoryEventType::Grabbed)
    ->withIncludeMovie(true);
$history = $radarr->history()->all(null, null, $filters);

// Get history since a specific date
$filters = HistoryOptions::default()->withIncludeMovie(true);
$records = $radarr->history()->since(new DateTime('2024-01-01'), $filters);

// Get history for a specific movie
$records = $radarr->history()->forMovie(1);

// Mark a history item as failed
$radarr->history()->markFailed(123);
```

### Queue Management

```php
use MartinCamen\Radarr\Data\Options\{PaginationOptions, QueueOptions, SortOptions};

// Get paginated queue (default: 50 items per page)
$queue = $radarr->queue()->all();

// Get queue with custom pagination
$pagination = new PaginationOptions(page: 1, pageSize: 100);
$queue = $radarr->queue()->all($pagination);

// Get queue with sorting
$sort = SortOptions::by('timeleft')->ascending();
$queue = $radarr->queue()->all(null, $sort);

// Include unknown movie items and movie details
$filters = QueueOptions::default()
    ->withIncludeUnknownMovieItems(true)
    ->withIncludeMovie(true);
$queue = $radarr->queue()->all(null, null, $filters);

// Get queue item by ID
$item = $radarr->queue()->get(1);

// Get queue status
$status = $radarr->queue()->status();
echo $status->totalCount;
echo $status->hasIssues();

// Delete item from queue
$radarr->queue()->delete(
    id: 1,
    removeFromClient: true,
    blocklist: false,
    skipRedownload: false,
    changeCategory: false,
);

// Bulk delete items from queue
$radarr->queue()->bulkDelete(
    ids: [1, 2, 3],
    removeFromClient: true,
    blocklist: false,
);
```

### Wanted (Missing & Cutoff)

```php
use MartinCamen\Radarr\Data\Options\{PaginationOptions, SortOptions, WantedOptions};

// Get paginated missing movies
$missing = $radarr->wanted()->missing();

// Get missing movies with custom pagination and sorting
$pagination = new PaginationOptions(page: 1, pageSize: 50);
$sort = SortOptions::by('title')->ascending();
$missing = $radarr->wanted()->missing($pagination, $sort);

// Filter to only monitored movies
$filters = WantedOptions::default()->onlyMonitored();
$missing = $radarr->wanted()->missing(null, null, $filters);

// Get ALL missing movies (automatically handles pagination)
$allMissing = $radarr->wanted()->allMissing();

// Get movies below quality cutoff
$cutoff = $radarr->wanted()->cutoff();

// Get ALL movies below quality cutoff
$allCutoff = $radarr->wanted()->allCutoff();
```

### System Information

```php
// Get system status
$status = $radarr->system()->status();
echo $status->version;
echo $status->appName;
echo $status->isProduction;

// Get disk space
$diskSpace = $radarr->system()->diskSpace();
foreach ($diskSpace as $disk) {
    echo $disk->path;
    echo $disk->freeSpace;
    echo $disk->totalSpace;
}

// Get health checks
$health = $radarr->system()->health();
foreach ($health as $check) {
    echo $check->source;
    echo $check->type->value; // HealthCheckType enum
    echo $check->message;
}
```

### Commands

```php
use MartinCamen\Radarr\Data\Enums\CommandName;

// Get all commands
$commands = $radarr->command()->all();

// Get command by ID
$command = $radarr->command()->get(1);

// Execute a refresh monitored downloads command
$command = $radarr->command()->execute(CommandName::RefreshMonitoredDownloads);

// Execute a movie search
$command = $radarr->command()->execute(
    CommandName::MoviesSearch,
    ['movieIds' => [1, 2, 3]],
);

// Execute a missing movies search
$command = $radarr->command()->execute(
    CommandName::MissingMoviesSearch,
    ['filterKey' => 'monitored', 'filterValue' => 'true'],
);
```

## Working with Collections

The SDK returns typed DTOs and collections:

```php
// Get movies as a typed collection
$movies = $radarr->movie()->all();

// Iterate with typed items
foreach ($movies as $movie) {
    echo $movie->title;        // string
    echo $movie->year;         // int
    echo $movie->tmdbId;       // int
    echo $movie->status->value; // MovieStatus enum
}

// Collection methods
$first = $movies->first();     // ?Movie
$last = $movies->last();       // ?Movie
$found = $movies->find(123);   // ?Movie by ID
$count = $movies->count();     // int
$isEmpty = $movies->isEmpty(); // bool

// Filter movies
$monitored = $movies->filter(fn($m) => $m->monitored);

// Map collection
$titles = $movies->map(fn($m) => $m->title);

// Convert to array
$array = $movies->toArray();
```

## Using Request Options

The SDK provides typed request option classes for better code clarity and reusability:

### Pagination Options

```php
use MartinCamen\Radarr\Data\Options\PaginationOptions;

// Default pagination (page 1, 10 items)
$options = PaginationOptions::default();

// Custom pagination
$options = new PaginationOptions(page: 2, pageSize: 50);

// Using fluent methods
$options = PaginationOptions::default()
    ->withPage(3)
    ->withPageSize(100);
```

### Sort Options

```php
use MartinCamen\Radarr\Data\Enums\SortDirection;
use MartinCamen\Radarr\Data\Options\SortOptions;

// No sorting
$options = SortOptions::none();

// Sort by key
$options = SortOptions::by('title');

// Sort with direction
$options = SortOptions::by('date', SortDirection::Descending);

// Using fluent methods
$options = SortOptions::by('title')->ascending();
$options = SortOptions::by('date')->descending();
```

### Filter Options

```php
use MartinCamen\Radarr\Data\Enums\HistoryEventType;
use MartinCamen\Radarr\Data\Options\{CalendarOptions, HistoryOptions, QueueOptions, WantedOptions};

// Calendar options
$options = CalendarOptions::default()
    ->withDateRange($start, $end)
    ->withUnmonitored(true)
    ->withTags([1, 2]);

// History options
$options = HistoryOptions::default()
    ->withEventType(HistoryEventType::Grabbed)
    ->withIncludeMovie(true)
    ->withMovieIds([1, 2, 3]);

// Queue options
$options = QueueOptions::default()
    ->withIncludeUnknownMovieItems(true)
    ->withIncludeMovie(true);

// Wanted options
$options = WantedOptions::default()->onlyMonitored();
$options = WantedOptions::default()->onlyUnmonitored();
```

## Using Enums

The SDK provides typed enums for better type safety:

```php
use MartinCamen\Radarr\Data\Enums\{CommandName, HistoryEventType, MovieStatus, SortDirection};

// Command names
$command = $radarr->command()->execute(CommandName::RefreshMovie);

// History event types
$filters = HistoryOptions::default()
    ->withEventType(HistoryEventType::MovieFileRenamed);

// Sort direction
$sort = SortOptions::by('title', SortDirection::Ascending);

// Movie status
if ($movie->status === MovieStatus::Released) {
    // Movie has been released
}
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use MartinCamen\ArrCore\Exceptions\{AuthenticationException, ConnectionException, NotFoundException, ValidationException};

try {
    $movie = $radarr->movie()->get(999);
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
use MartinCamen\Radarr\Testing\Factories\{DownloadFactory, MovieFactory, SystemStatusFactory};
use MartinCamen\Radarr\Testing\RadarrFake;

class MyTest extends TestCase
{
    #[Test]
    public function it_displays_movies(): void
    {
        // Create a fake Radarr instance with mock responses
        $fake = new RadarrFake([
            'movies' => MovieFactory::makeMany(5),
            'systemStatus' => SystemStatusFactory::make(),
        ]);

        // Use SDK-layer methods
        $movies = $fake->movies();

        $this->assertCount(5, $movies);

        // Assert method calls were made
        $fake->assertCalled('movies');
        $fake->assertCalledTimes('movies', 1);
    }
}
```

### Using Factories

```php
use MartinCamen\Radarr\Testing\Factories\{DownloadFactory, HistoryFactory, MovieFactory, SystemStatusFactory};

// Create a single movie array
$movie = MovieFactory::make(1);

// Create multiple movies
$movies = MovieFactory::makeMany(5);

// Create with custom attributes
$movie = MovieFactory::make(123, [
    'title' => 'Inception',
    'year'  => 2010,
]);

// Create download records
$downloads = DownloadFactory::makeMany(3);

// Create download with specific state
$completed = DownloadFactory::makeCompleted(1);
$withError = DownloadFactory::makeWithError(2);
```

## Available Actions

### MovieActions

| Method | Description |
|--------|-------------|
| `all(?int $tmdbId = null)` | Get all movies, optionally filter by TMDb ID |
| `get(int $id)` | Get a specific movie by ID |
| `lookup(string $term)` | Search for movies by title, IMDb ID, or TMDb ID |
| `lookupByTmdb(int $tmdbId)` | Lookup movie by TMDb ID |
| `lookupByImdb(string $imdbId)` | Lookup movie by IMDb ID |
| `add(array $movieData)` | Add a new movie |
| `update(int $id, array $movieData)` | Update an existing movie |
| `delete(int $id, bool $deleteFiles = false, bool $addImportExclusion = false)` | Delete a movie |

### CalendarActions

| Method | Description |
|--------|-------------|
| `get(?CalendarOptions $options = null)` | Get upcoming movies within a date range |
| `getById(int $id)` | Get calendar event by ID |

### HistoryActions

| Method | Description |
|--------|-------------|
| `all(?PaginationOptions $pagination, ?SortOptions $sort, ?HistoryOptions $filters)` | Get paginated history |
| `since(DateTimeInterface $date, ?HistoryOptions $filters)` | Get history since a specific date |
| `forMovie(int $movieId, ?HistoryOptions $filters)` | Get history for a specific movie |
| `markFailed(int $id)` | Mark a history item as failed |

### QueueActions

| Method | Description |
|--------|-------------|
| `all(?PaginationOptions $pagination, ?SortOptions $sort, ?QueueOptions $filters)` | Get paginated queue |
| `get(int $id)` | Get queue item by ID |
| `status()` | Get queue status (counts, errors, warnings) |
| `delete(int $id, ...)` | Delete item from queue |
| `bulkDelete(array $ids, ...)` | Bulk delete items from queue |

### WantedActions

| Method | Description |
|--------|-------------|
| `missing(?PaginationOptions $pagination, ?SortOptions $sort, ?WantedOptions $filters)` | Get paginated missing movies |
| `allMissing(?WantedOptions $filters)` | Get ALL missing movies (auto-paginates) |
| `cutoff(?PaginationOptions $pagination, ?SortOptions $sort, ?WantedOptions $filters)` | Get movies below quality cutoff |
| `allCutoff(?WantedOptions $filters)` | Get ALL movies below quality cutoff (auto-paginates) |

### SystemActions

| Method | Description |
|--------|-------------|
| `status()` | Get system status |
| `diskSpace()` | Get disk space information |
| `health()` | Get health checks |

### CommandActions

| Method | Description |
|--------|-------------|
| `all()` | Get all commands |
| `get(int $id)` | Get command by ID |
| `execute(CommandName $name, array $params = [])` | Execute a command |

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
