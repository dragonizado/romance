<?php 

namespace Dragonizado\Romance\Facades;

use Illuminate\Support\Facades\Facade;

class ApiRomance extends Facade
{
    protected static function getFacadeAccessor(){
        return 'api-romance';
    }
}


?>