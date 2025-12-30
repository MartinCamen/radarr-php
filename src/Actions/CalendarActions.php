<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Data\Enums\CalendarEndpoint;
use MartinCamen\Radarr\Data\Options\CalendarOptions;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/** @link https://radarr.video/docs/api/#/Calendar */
final readonly class CalendarActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get upcoming movies within a date range.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar
     */
    public function get(?CalendarOptions $options = null): MovieCollection
    {
        $params = $options?->toArray() ?? [];

        $result = $this->client->get(CalendarEndpoint::All, $params);

        return MovieCollection::fromArray($result);
    }

    /**
     * Get calendar event by ID.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar__id_
     */
    public function getById(int $id): Movie
    {
        $result = $this->client->get(CalendarEndpoint::ById, ['id' => $id]);

        return Movie::fromArray($result);
    }
}
