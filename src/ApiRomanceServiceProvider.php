<?php
namespace Dragonizado\Romance;

use Illuminate\Support\ServiceProvider;

class ApiRomanceServiceProvider extends ServiceProvider{

    public function register(){
        $this->app->bind('api-romance',function(){
            return new \Dragonizado\Romance\RomanceApi(
                env('DRAGONIZADO_API_ROMANCE_KEY'),
                env('DRAGONIZADO_API_ROMANCE_URL'),
                env('DRAGONIZADO_API_ROMANCE_DEBUG')
                ) ;
        });
    }

    public function boot(){

    }
}
