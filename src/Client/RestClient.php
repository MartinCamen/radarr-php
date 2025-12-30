<?php

namespace MartinCamen\Radarr\Client;

use MartinCamen\ArrCore\Client\RestClient as CoreRestClient;
use MartinCamen\ArrCore\Client\RestClientInterface;

/** @link https://radarr.video/docs/api/ */
class RestClient extends CoreRestClient implements RestClientInterface {}
