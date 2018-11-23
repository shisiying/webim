<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/11/17
 * Time: 16:31
 */

namespace App\ChatTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use App\Services\ChatService;


class ChatTask extends Task
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $swoole = app('swoole');
        $pushMsg = array('code'=>0,'msg'=>'','data'=>array());
        $data = json_decode($this->data,true);

        switch( $data['task'] ){
            case 'open':
                $pushMsg = ChatService::open( $data );
                if (is_array($pushMsg)){
                    var_dump($pushMsg);
                }
                $swoole->push( $data['fd'] , json_encode($pushMsg) );
                return 'Finished';
            case 'nologin':
                $pushMsg = ChatService::noLogin( $data );
                $swoole->push( $data['fd'] ,json_encode($pushMsg));
                return "Finished";
            case 'login':
                $pushMsg = ChatService::doLogin( $data );
                break;
            case 'logout':
                $pushMsg = ChatService::doLogout($data);
                break;
            case 'change':
                $pushMsg = ChatService::change( $data );
                break;
            case 'new':
                $pushMsg = ChatService::sendNewMsg( $data );
                break;
            case 'secretnew':
                $pushMsg = ChatService::sendSecretMsg( $data );
                //对方在线才可以私聊信息
                if ($pushMsg){
                    $this->sendMsgBySecret($swoole,$pushMsg,$data['send_fd']);
                }
                return "Finished";
        }
        if ($pushMsg){
            $this->sendMsg($swoole,$pushMsg,$data['fd']);
        }
        return "Finished";
    }

    // 可选的，完成事件，任务处理完后的逻辑，运行在Worker进程中，可以投递任务
    public function finish()
    {
        $data = json_decode($this->data,true);
        if ($data['task']=='new'){
            Task::deliver(new ServerChanTask($data)); // 投递其他任务
        }
    }

    //群发，广播,给所有人
    private function sendMsg($swoole,$pushMsg,$myfd){

        echo "当前服务器共有 ".count(app('swoole')->ws_usersTable). " 个连接\n";
        foreach(app('swoole')->ws_usersTable as $key => $row) {
            if($row['fd'] === $myfd){
                $pushMsg['data']['mine'] = 1;
            } else {
                $pushMsg['data']['mine'] = 0;
            }

            $swoole->push($row['fd'], json_encode($pushMsg));
        }
    }

    //只广播给同个房间的人
    private function sendMsgOnlyRoom($swoole,$pushMsg,$myfd)
    {
        $userArr = app('swoole')->ws_roomsTable->get($pushMsg['data']['roomid']);
        if ($userArr) {
            $userArr = json_decode($userArr['users'], true);
            echo "当前房间共有 " . count($userArr) . " 个连接\n";
            foreach ($userArr as $row){
                if($row === $myfd){
                    $pushMsg['data']['mine'] = 1;
                } else {
                    $pushMsg['data']['mine'] = 0;
                }
                $swoole->push($row, json_encode($pushMsg));
            }
        }
    }

    //私聊发信息
    private function sendMsgBySecret($swoole,$pushMsg,$myfd)
    {
        //判断用户是否在线
        $user = app('swoole')->ws_usersTable->get('user'.$pushMsg['data']['receive_fd']);
        if ($user){
            //发送给发送方
            if ($myfd == $pushMsg['data']['send_fd']){
                $pushMsg['data']['mine'] = 1;
                $swoole->push($pushMsg['data']['send_fd'], json_encode($pushMsg));
            }

            //发送给接受方
            $pushMsg['data']['mine'] = 0;
            $swoole->push($pushMsg['data']['receive_fd'], json_encode($pushMsg));
        }
    }
}