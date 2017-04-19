<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WxApiService;

class WxAuthProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     * @author LaravelAcademy.org
     */
    public function register()
    {
        $config = [
            'appid'         => env( 'WX_APPID' ),
            'appsecret'     => env( 'WX_APPSECRET' ),
            'token'         => env( 'WX_TOKEN' ),
            'encodingaeskey'=> env( 'WX_ENCODINGAESKEY' ),
        ];
        //使用singleton绑定单例
        $this->app->singleton('wxAuth',function() use ( $config ) {
            return new WxApiService( $config );
        });

        //使用bind绑定实例到接口以便依赖注入
        $this->app->bind('App\Services\WxApiService',function() use ( $config ) {
            return new WxApiService( $config );
        });
    }
}