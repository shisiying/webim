<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/11/19
 * Time: 11:17
 */
namespace App\ChatTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Seven;

class ServerChanTask extends Task{

    protected $channel=[];
    private $data;

    public function __construct($data)
    {
        $this->channel = [
            'a'=>config('serverchan.a'), //sw群组
            'b'=>config('serverchan.b'), //php群组
            'c'=>config('serverchan.c') //go群组
        ];

        $this->data = $data;

    }

    public function handle()
    {
        $domain = env('APP_URL');
        $message = $this->data['params']['name']."说了：\n".$this->data['message']."\n";
        $message .= "来自于"."[sevenshi的webim](".$domain.")"."\n";

        switch ($this->data['roomid']){
            case 'a':
                try{
                    $response = Seven::setTitle('来自sw群组')->setMessage($message)->setChannel($this->channel['a'])->pushbear();
                }catch (\Exception $e){
                    echo $e->getMessage();
                }
                break;
            case 'b':
                try{
                    $response = Seven::setTitle('来自php群组')->setMessage($message)->setChannel($this->channel['b'])->pushbear();
                }catch (\Exception $e){
                    echo $e->getMessage();
                }
                break;
            case 'c':
                try{
                    $response = Seven::setTitle('来自go群组')->setMessage($message)->setChannel($this->channel['c'])->pushbear();
                }catch (\Exception $e){
                    echo $e->getMessage();
                }
                break;

            default:
                break;
        }
    }
}