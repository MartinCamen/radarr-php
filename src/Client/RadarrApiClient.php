<?php

declare(strict_types=1);

namespace MartinCamen\Radarr\Client;

use MartinCamen\ArrCore\Actions\SystemActions;
use MartinCamen\ArrCore\Actions\WantedActions;
use MartinCamen\ArrCore\Client\RestClient;
use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Actions\QueueActions;
use MartinCamen\Radarr\Config\RadarrConfiguration;

/**
 * Low-level Radarr API client.
 *
 * This class provides direct access to the Radarr REST API using
 * Radarr's native terminology (queue, movie, wanted, etc.).
 *
 * For most use cases, prefer using the high-level Radarr class instead,
 * which provides a unified API with Core domain models.
 *
 * @internal This class is primarily for internal use. Use Radarr class for public API.
 *
 * @link https://radarr.video/docs/api/
 */
class RadarrApiClient implements RadarrApiClientInterface
{
    protected RestClientInterface $client;
    protected ?MovieActions $movieActions = null;
    protected ?QueueActions $queueActions = null;
    protected ?HistoryActions $historyActions = null;
    protected ?CalendarActions $calendarActions = null;
    protected ?SystemActions $systemActions = null;
    protected ?CommandActions $commandActions = null;
    protected ?WantedActions $wantedActions = null;

    public function __construct(
        string $host = 'localhost',
        int $port = 7878,
        string $apiKey = '',
        bool $useHttps = false,
        int $timeout = 30,
        string $urlBase = '',
        ?RestClientInterface $restClient = null,
    ) {
        $config = new RadarrConfiguration(
            host: $host,
            port: $port,
            apiKey: $apiKey,
            useHttps: $useHttps,
            timeout: $timeout,
            urlBase: $urlBase,
        );

        $this->client = $restClient ?? new RestClient($config);
    }

    public static function make(RadarrConfiguration $config): self
    {
        return new self(
            host: $config->host,
            port: $config->port,
            apiKey: $config->apiKey,
            useHttps: $config->useHttps,
            timeout: $config->timeout,
            urlBase: $config->urlBase,
        );
    }

    public function movie(): MovieActions
    {
        return $this->movieActions ??= new MovieActions($this->client);
    }

    public function queue(): QueueActions
    {
        return $this->queueActions ??= new QueueActions($this->client);
    }

    public function history(): HistoryActions
    {
        return $this->historyActions ??= new HistoryActions($this->client);
    }

    public function calendar(): CalendarActions
    {
        return $this->calendarActions ??= new CalendarActions($this->client);
    }

    public function system(): SystemActions
    {
        return $this->systemActions ??= new SystemActions($this->client);
    }

    public function command(): CommandActions
    {
        return $this->commandActions ??= new CommandActions($this->client);
    }

    public function wanted(): WantedActions
    {
        return $this->wantedActions ??= new WantedActions($this->client);
    }

    public function getClient(): RestClientInterface
    {
        return $this->client;
    }
}
