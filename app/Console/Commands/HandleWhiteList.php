<?php
/**
 * 控制台命令处理授权白名单
 * @author Jackson <i@joosie.cn>
 * Date 2017年10月16日17:42:25
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
* 控制台授权白名单命令处理类
*/
class HandleWhiteList extends Command
{
    /**
     * 控制台执行的命令
     * @var string
     */
    protected $signature = 'whitelist:make {type} {system}';

    /**
     * 命令介绍
     * @var string
     */
    protected $description = 'Use the command, you could manage system whitelist!';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 命令处理入口
     */
    public function handle()
    {
        $type = $this->argument( 'type' );
        $system = $this->argument( 'system' );
        switch ( $type )
        {
            case 'create':
                $this->create($system);
                break;
            case 'remove':
                $this->remove($system);
                break;
            default:
                $this->log( 'Please use the right command.' );
                $this->log( 'Ex: php artisan whitelist:make [create|remove] {system}' );
                exit;
        }
    }

    /**
     * 创建新的白名单用户
     * @param  string $system 白名单用户标识
     */
    private function create( $system )
    {
        $nonce = $this->getNonceStr();
        $info = DB::table( 'allowed_list' )->where( 'system', $system )->first();
        if ( !empty( $info ) )
            return $this->log( 'The system name already exist in whitelist!', 'ERROR' );

        $attributes = [
            'system' => $system,
            'token' => $nonce,
        ];
        if ( !DB::table( 'allowed_list' )->insert( $attributes ) )
            $this->log( 'Create a new system to whitelist failed', 'ERROR' );
        else
        {
            $this->log( 'Create a new system to whitelist success' );
            $this->log( sprintf( 'This is %s\'s auth token, please remember', $system ) );
            $this->log( sprintf( 'Auth token: %s', $nonce ), 'SUCCESS' );
            return;
        }
    }

    /**
     * 移除一个白名单用户
     * @param  string $system 白名单用户标识
     */
    private function remove( $system )
    {
        $info = DB::table( 'allowed_list' )->where( 'system', $system )->first();
        if ( empty( $info ) )
            return $this->log( sprintf( 'This %s is not exist in whitelist!', $system ), 'ERROR' );

        if ( !DB::table( 'allowed_list' )->where( 'system', $system )->delete() )
            return $this->log( sprintf( 'Remove the %s failed from whitelist', $system ), 'ERROR' );
        else
            return $this->log( sprintf( 'Remove the %s success from whitelist', $system ) );
    }

    /**
     * 获取 Nonce 随机字符串
     * @param  integer $len 字符串长度
     * @return string
     */
    private function getNonceStr( $len = 30 )
    {
        $nonce = '';
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ( $i = 0; $i <= $len; $i++ )
            $nonce .= $str[rand( 0, strlen( $str ) - 1 )];
        return $nonce;
    }

    /**
     * 日志输出
     * @param  string $content 输出内容
     * @param  string $lv      内容级别[INFO|SUCCESS|ERROR]
     */
    private function log( $content, $lv = 'INFO' )
    {
        if ( $lv === 'INFO' )
            echo sprintf( '%s' . PHP_EOL, $content );
        elseif ( $lv === 'ERROR' )
            echo sprintf( "\033[31m%s\033[0m" . PHP_EOL, $content );
        elseif ( $lv === 'SUCCESS' )
            echo sprintf( "\033[32m%s\033[0m" . PHP_EOL, $content );
        else
            echo sprintf( '%s' . PHP_EOL, $content );
    }
}