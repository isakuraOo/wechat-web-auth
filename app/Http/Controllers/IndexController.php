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
        $this->wxApi = app( 'wxApi' );
    }

    /**
     * 授权入口
     */
    public function auth( Request $request )
    {
        $callback = $request->input( 'redirect_url' );
        $state = base64_encode( $callback );
        $notifyUrl = sprintf( 'http://%s/notify', $_SERVER['HTTP_HOST'] );
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
        $callback = urldecode( base64_decode( $state ) );

        $oauthRes = $this->wxApi->getOauthAccessToken();
        if ( $oauthRes === false )
            return redirect( $this->buildUrl( $callback, ['msg' => $this->wxApi->errMsg] ) );

        $userinfo = $this->wxApi->getOauthUserinfo( $oauthRes['access_token'], $oauthRes['openid'] );
        if ( $userinfo === false )
            return redirect( $this->buildUrl( $callback, ['msg' => $this->wxApi->errMsg] ) );

        $userinfoRes = $this->saveUserinfo( $userinfo );
        if ( $userinfoRes === false )
            return redirect( $this->buildUrl( $callback, ['msg' => 'Faild! Save wx user info!'] ) );

        $accessToken = $this->generateAccessToken( $userinfo['openid'] );
        if ( $accessToken === false )
            return redirect( $this->buildUrl( $callback, ['msg' => 'Faild! Generate access token'] ) );

        return redirect( $this->buildUrl( $callback, ['access_token' => $accessToken] ) );
    }

    /**
     * 获取授权用户数据
     */
    public function userinfo( Request $request )
    {
        $token = $request->input( 'access_token', '' );
        $tokenInfo = DB::table( 'access_token' )->where( 'token', $token )->first();
        if ( empty( $tokenInfo ) || $tokenInfo->invalid_time < time() )
            return response()->json( ['errcode' => 1, 'msg' => 'Invalid access token!'] );

        $userinfo = DB::table( 'wx_user_info' )
            ->select( 'nickname', 'sex', 'province', 'city', 'country', 'headimgurl' )
            ->where( 'openid', $tokenInfo->openid )
            ->first();
        if ( empty( $userinfo ) )
            return response()->json( ['errcode' => 1, 'msg' => 'Not exist user info!'] );
        else
            return response()->json( ['errcode' => 0, 'msg' => 'Successfully!', 'data' => get_object_vars( $userinfo )] );
    }

    /**
     * 根据参数数组创建 url
     * @param string $url 原 url 地址
     * @param arrar $params 需要添加到 url 的参数数组
     * @return string 创建成功的新 url
     */
    private function buildUrl( $url, $params = [] )
    {
        if ( empty( $params ) )
            return $url;
        $params = http_build_query( $params );
        return $url . ( ( strpos($url, '?') ) ? '&' : '?' ) . $params;
    }

    /**
     * 保存微信用户数据
     * @param  array $data 从微信接口获取的用户基本信息数组
     * $data = [
     *     'openid' => 'OpenID'
     *     'nickname' => '昵称',
     *     'sex' => 0,
     *     'province' => '广东省',
     *     'city' => '深圳市',
     *     'country' => '中国',
     *     'headimgurl' => 'url',
     * ]
     * @return boolean
     */
    private function saveUserinfo( $data )
    {
        $attributes = [
            'openid'        => $data['openid'],
            'nickname'      => $data['nickname'],
            'sex'           => $data['sex'],
            'province'      => $data['province'],
            'city'          => $data['city'],
            'country'       => $data['country'],
            'headimgurl'    => $data['headimgurl'],
            'update_time'   => time(),
        ];
        $info = DB::table( 'wx_user_info' )->where( 'openid', $data['openid'] )->first();
        if ( empty( $info ) )
        {
            $attributes['create_time'] = time();
            $res = DB::table( 'wx_user_info' )->insert( $attributes );
        }
        else
            $res = DB::table( 'wx_user_info' )->update( $attributes );
        return $res;
    }

    /**
     * 生成访问令牌
     * @param  string  $openid 微信 OpenID
     * @param  integer $len    长度
     * @param  integer $expire 令牌生命周期
     * @return string|false          
     */
    private function generateAccessToken( $openid, $len = 32, $expire = 7200 )
    {
        $token = '';
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ( $i = 0; $i <= $len; $i++ )
            $token .= $str[rand( 0, strlen( $str ) - 1 )];
        $data = ['token' => $token, 'openid' => $openid, 'create_time' => time(), 'invalid_time' => time() + $expire];
        $res = DB::table( 'access_token' )->insert( $data );
        return $res ? $token : false;
    }
}