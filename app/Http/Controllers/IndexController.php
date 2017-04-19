<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
* 主控制器
*/
class IndexController extends Controller
{
    // 微信 SDK 实例
    public $wxApi;

    public function __construct()
    {
        $this->wxApi = app( 'wxAuth' );
    }

    public function auth( Request $request )
    {
        $callback = $request->input( 'redirect_url' );
        $state = base64_encode( $callback );
        $notifyUrl = sprintf( 'http://%s/notify', $_SERVER['HTTP_POST'] );
        $authUrl = sprintf( "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s#wechat_redirect", env( 'WX_APPID' ), urlencode( $notifyUrl ), $state );
        return redirect( $authUrl );
    }

    /**
     * 微信回调处理
     */
    public function notify( Request $request )
    {
        $state = $request->input( 'state' );
        $code = $request->input( 'code' );
        $callback = base64_decode( $state );
        if ( strpos( $callback, '?' ) !== false )
            $callback .= sprintf( '&code=%s', $code );
        else
            $callback .= sprintf( '?code=%s', $code );
        return redirect( $callback );
    }
}