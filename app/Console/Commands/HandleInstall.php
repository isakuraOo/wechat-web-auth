<?php
/**
 * @author Jackson <i@joosie.cn>
 * Date 2017年10月18日23:50:33
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
* 系统安装命令处理类
*/
class HandleInstall extends Command
{
    /**
     * 数据库文件路径
     */
    const DB_FILE = './database/sql/wx_auth.sql';

    /**
     * 控制台执行的命令
     * @var string
     */
    protected $signature = 'system:install';

    /**
     * 命令介绍
     * @var string
     */
    protected $description = 'On the first time, please use the command to install system.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 命令处理入口
     */
    public function handle()
    {
        $this->log( 'Start create database...' );
        $file = file_get_contents( self::DB_FILE );
        $sqlArr = explode(';', $file);
        foreach ( $sqlArr as $sql )
        {
            $sql = trim( $sql, "\n" );
            if ( !empty( $sql ) )
            {
                $this->log( 'Now execute the sql:', 'SUCCESS' );
                $this->log( $sql );
                DB::statement( $sql );
            }
        }
        $this->log( 'Successfully! System is installed.' );
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