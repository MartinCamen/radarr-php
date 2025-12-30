<?php

namespace MartinCamen\Radarr\Data\Options;

interface RequestOptions
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
