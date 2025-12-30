<?php

namespace MartinCamen\Radarr\Data\Enums;

enum MovieStatus: string
{
    case Tba = 'tba';
    case Announced = 'announced';
    case InCinemas = 'inCinemas';
    case Released = 'released';
    case Deleted = 'deleted';
}
