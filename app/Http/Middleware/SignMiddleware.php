<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class SignMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->only( ['system', 'nonce', 'timestamp', 'signature'] );
        if ( !$this->sign( $data ) )
            return response()->json( ['errcode' => 1, 'Invalid sign!'] );
        return $next( $request );
    }

    /**
     * 签名验证
     * @param  array $data 提交参数
     * @return [type]       [description]
     */
    private function sign( $data )
    {
        $sign = $data['signature'];
        unset( $data['signature'] );
        ksort( $data );
        $data['token'] = DB::table( 'allowed_list' )->where( 'system', $data['system'] )->value( 'token' );
        if ( empty( $data['token'] ) )
            return false;

        $tmpStr = http_build_query( $data );
        return $sign === sha1( $tmpStr );
    }
}
