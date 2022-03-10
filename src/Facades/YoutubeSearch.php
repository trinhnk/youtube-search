<?php

namespace Trinhnk\YoutubeSearch\Facades;

use Illuminate\Support\Facades\Facade;

class YoutubeSearch extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'youtube-search';
    }
}
