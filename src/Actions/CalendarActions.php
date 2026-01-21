<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Actions\CalendarActions as CoreCalendarActions;
use MartinCamen\Radarr\Data\Options\CalendarOptions;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/** @link https://radarr.video/docs/api/#/Calendar */
final readonly class CalendarActions extends CoreCalendarActions
{
    /**
     * Get upcoming movies within a date range.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar
     */
    public function all(?CalendarOptions $calendarOptions = null): MovieCollection
    {
        return MovieCollection::fromArray($this->getAll($calendarOptions));
    }

    /**
     * Get calendar event by ID.
     *
     * @link https://radarr.video/docs/api/#/Calendar/get_api_v3_calendar__id_
     */
    public function find(int $id): Movie
    {
        return Movie::fromArray($this->getById($id));
    }
}
