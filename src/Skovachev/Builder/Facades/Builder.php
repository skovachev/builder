<?php namespace Skovachev\Builder\Facades;

use Illuminate\Support\Facades\Facade;

class Builder extends Facade {

    protected static function getFacadeAccessor() { return 'builder_service'; }

}