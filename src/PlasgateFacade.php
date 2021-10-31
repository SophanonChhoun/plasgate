<?php
namespace Lyly\Plasgate;

use Illuminate\Support\Facades\Facade;

class PlasgateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'plasgate';
    }
}
