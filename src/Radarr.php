<?php

namespace MartinCamen\Radarr;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Actions\CalendarActions;
use MartinCamen\Radarr\Actions\CommandActions;
use MartinCamen\Radarr\Actions\HistoryActions;
use MartinCamen\Radarr\Actions\MovieActions;
use MartinCamen\Radarr\Actions\QueueActions;
use MartinCamen\Radarr\Actions\SystemActions;
use MartinCamen\Radarr\Actions\WantedActions;
use MartinCamen\Radarr\Client\RestClient;
use MartinCamen\Radarr\Config\RadarrConfiguration;

class Radarr implements RadarrInterface
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
