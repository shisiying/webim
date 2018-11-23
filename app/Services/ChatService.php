<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/11/17
 * Time: 16:20
 */
namespace App\Services;
use App\Services\ChatUsersService;

class ChatService
{
    public static function noLogin($data){
        $pushMsg['code'] = 5;
        $pushMsg['msg'] = "系统不会存储您的Email，只是为了证明你是一个地球人";
        if( !$data['params']['name']){
            $pushMsg['msg'] = "输入一个昵称或许可以让更多人的人了解你";
        }
        $pushMsg['data']['mine'] = 1;
        unset( $data );
        return $pushMsg;
    }

    //打开连接
    public static function open( $data ){
        //返回4代表初始化房间以及获取在线用户
        $pushMsg['code'] = 4;
        $pushMsg['msg'] = 'success';
        $pushMsg['data']['mine'] = 0;
        $pushMsg['data']['rooms'] = self::getRooms();
        $pushMsg['data']['users'] = self::getOnlineUsers();
        unset( $data );
        return $pushMsg;
    }

    //获取房间
    public static function getRooms(){
        $rooms = config('chat.rooms');
        $roomss = array();
        foreach($rooms as $_k => $_v){
            $roomss[] = array(
                'roomid'   => $_k,
                'roomname' => $_v
            );
        }
        return $roomss;
    }

    /**
     * @return array|mixed
     * swoole_table版本
     */
    public static function getOnlineUsers(){
        $user = new ChatUsersService();
        $lists = $user->getOnlineUsers();
        return $lists;
    }

    //登陆
    public static function doLogin($data)
    {
        $domain = config('chat.domain');
        $pushMsg['code'] = 1;
        $pushMsg['msg'] = $data['params']['name']."加入了群聊";

        $pushMsg['data']['roomid'] = $data['roomid'];
        $pushMsg['data']['fd'] = $data['fd'];
        $pushMsg['data']['name'] = $data['params']['name'];
        $pushMsg['data']['avatar'] = $domain.'images/avatar/f1/f_'.rand(1,12).'.jpg';
        $pushMsg['data']['time'] = date("H:i",time());
        //增加房间的名字
        $pushMsg['data']['roomname'] = config('chat.rooms')[$data['roomid']];

        self::login($data['roomid'],$data['fd'],$data['params']['name'],$data['params']['email'],$pushMsg['data']['avatar']);
        unset( $data );
        return $pushMsg;
    }

    //登陆写入swoole_table中
    public static function login($roomid,$fd,$name,$email,$avatar){
        if($name == ""){
            $name = '游客'.time();
        }
        if($email == ""){
            $email = 'xxx@qq.com';
        }
        if(!$name || !$email){

            throw new Exception('Fill in all the required fields.');
        }
        $user = new ChatUsersService(array(
            'roomid'    => $roomid,
            'fd'        => $fd,
            'name'		=> htmlspecialchars($name),
            'email'		=> $email,
            'avatar'	=> $avatar
        ));
        if(!$user->save()){
            throw new Exception('This nick is in use.');
        }
    }

    //登出
    public static function doLogout($data)
    {
        echo "退出################";
        var_dump($data);
        $roomid = $data['params']['roomid'];

        //从房间里删除用户
        $userArr = app('swoole')->ws_roomsTable->get($roomid);
        if ($userArr){
            $userArr = json_decode($userArr['users'],true);
            $key=array_search($data['fd'],$userArr);
            array_splice($userArr,$key,1);
            app('swoole')->ws_roomsTable->set($roomid, ['users' => json_encode($userArr)]);
        }


        //从房间用户信息删除
        $infos = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$roomid);
        var_dump($infos);
        if ($infos){
            $infos = json_decode($infos['infos'],true);
            if (!empty($infos)){
                foreach ($infos as $info_key => $row){
                    if ($row['fd']==$data['fd']){
                        array_splice($infos,$info_key,1);
                        break;
                    }
                }
                var_dump($infos);
                app('swoole')->ws_roomUsersTable->set('roomUsersInfo'.$roomid,['infos'=>json_encode($infos)]);
            }
        }
        echo "退出结束################";


        //删除用户
        app('swoole')->ws_usersTable->del('user'.$data['fd']);

        $pushMsg['code'] = 3;
        $pushMsg['msg'] = $data['params']['name']."退出了群聊";
        $pushMsg['data']['fd'] = $data['fd'];
        $pushMsg['data']['name'] = $data['params']['name'];
        $pushMsg['data']['roomid'] = $roomid;
        unset( $data );
        return $pushMsg;
    }

    //改变房间
    public static function change( $data ){
        $pushMsg['code'] = 6;
        $pushMsg['msg']  = '换房成功';
        $user = new ChatUsersService(array(
            'roomid'    => $data['roomid'],//新的房间号
            'fd'        => $data['fd'],
            'name'		=> htmlspecialchars($data['params']['name']),
            'email'		=> $data['params']['email'],
            'avatar'	=> $data['params']['avatar']
        ));

        $is_copyed = $user->changeUser($data['oldroomid'],$data['fd'],$data['roomid']);

        if($is_copyed){
            $pushMsg['data']['oldroomid'] = $data['oldroomid'];
            $pushMsg['data']['roomid'] = $data['roomid'];
            $pushMsg['data']['mine'] = 0;
            $pushMsg['data']['fd'] = $data['fd'];
            $pushMsg['data']['name'] = $data['params']['name'];
            $pushMsg['data']['avatar'] = $data['params']['avatar'];
            $pushMsg['data']['time'] = date("H:i",time());
            unset( $data );
            return $pushMsg;
        }

        return false;

    }

    //发送消息
    public static function sendNewMsg( $data ){
        $pushMsg['code'] = 2;
        $pushMsg['msg'] = "";
        $pushMsg['data']['roomid'] = $data['roomid'];
        $pushMsg['data']['fd'] = $data['fd'];
        $pushMsg['data']['name'] = $data['params']['name'];
        $pushMsg['data']['avatar'] = $data['params']['avatar'];
        $pushMsg['data']['newmessage'] = self::escape(htmlspecialchars($data['message']));
        $pushMsg['data']['remains'] = array();
        if($data['c'] == 'img'){
            $pushMsg['data']['newmessage'] = '<img class="chat-img" onclick="preview(this)" style="display: block; max-width: 120px; max-height: 120px; visibility: visible;" src='.$pushMsg['data']['newmessage'].'>';
        } else {
            $emotion = config('chat.emotion');
            foreach($emotion as $_k => $_v){
                $pushMsg['data']['newmessage'] = str_replace($_k,$_v,$pushMsg['data']['newmessage']);
            }
            $tmp = self::remind($data['roomid'],$pushMsg['data']['newmessage']);
            if($tmp['flag']){
                $pushMsg['data']['newmessage'] = $tmp['msg'];
                $pushMsg['data']['remains'] = $tmp['remains'];
            }
            unset( $tmp );
        }
        $pushMsg['data']['time'] = date("H:i",time());
        unset( $data );
        return $pushMsg;
    }

    //私聊发信息
    public static function sendSecretMsg($data)
    {
        //获取接受人信息
        if ( $receiver = app('swoole')->ws_usersTable->get('user'.$data['receive_fd'])){
            $pushMsg['code'] = 7;
            $pushMsg['msg'] = "";

            //发送人信息
            $pushMsg['data']['send_fd'] = $data['send_fd'];
            $pushMsg['data']['send_name'] = $data['params']['name'];
            $pushMsg['data']['send_avatar'] = $data['params']['avatar'];
            $pushMsg['data']['newmessage'] = self::escape(htmlspecialchars($data['message']));
            $pushMsg['data']['remains'] = array();

            //接受人信息
            $pushMsg['data']['receive_fd'] = $data['receive_fd'];
            $pushMsg['data']['receive_name'] = $receiver['name'];
            $pushMsg['data']['receive_avatar'] = $receiver['avatar'];


            if($data['c'] == 'img'){
                $pushMsg['data']['newmessage'] = '<img class="chat-img" onclick="preview(this)" style="display: block; max-width: 120px; max-height: 120px; visibility: visible;" src='.$pushMsg['data']['newmessage'].'>';
            } else {
                $emotion = config('chat.emotion');
                foreach($emotion as $_k => $_v){
                    $pushMsg['data']['newmessage'] = str_replace($_k,$_v,$pushMsg['data']['newmessage']);
                }
            }
            $pushMsg['data']['time'] = date("H:i",time());
            unset( $data );
            return $pushMsg;
        }

        return false;


    }
    //匹配文本
    public static function remind($roomid,$msg){
        $data = array();
        if( $msg != ""){
            $data['msg'] = $msg;
            //正则匹配出所有@的人来
            $s = preg_match_all( '~@(.+?)　~' , $msg, $matches  ) ;
            $data['flag'] = false;
            if($s){
                $data['flag'] = true;
                $m1 = array_unique( $matches[0] );
                $m2 = array_unique( $matches[1] );
                $user = new ChatUsersService();
                $users = $user->getUsersByRoom($roomid);
                $m3 = array();
                foreach($users as $_k => $_v){
                    $m3[$_v['name']] = $_v['fd'];
                }
                $i = 0;
                foreach($m2 as $_k => $_v){
                    if(array_key_exists($_v,$m3)){
                        $data['msg'] = str_replace($m1[$_k],'<font color="blue">'.trim($m1[$_k]).'</font>',$data['msg']);
                        $data['remains'][$i]['fd'] = $m3[$_v];
                        $data['remains'][$i]['name'] = $_v;
                        $i++;
                    }
                }
                unset($users);
                unset($m1,$m2,$m3);
            }
        }
        return $data;
    }

    //过滤文本
    public static function escape($input, $urldecode = 0) {
        if(is_array($input)){
            foreach($input as $k=>$v){
                $input[$k]=escape($v,$urldecode);
            }
        }else{
            $input=trim($input);
            if ($urldecode == 1) {
                $input=str_replace(array('+'),array('{addplus}'),$input);
                $input = urldecode($input);
                $input=str_replace(array('{addplus}'),array('+'),$input);
            }
            // PHP版本大于5.4.0，直接转义字符
            if (strnatcasecmp(PHP_VERSION, '5.4.0') >= 0) {
                $input = addslashes($input);
            } else {
                // 魔法转义没开启，自动加反斜杠
                if (!get_magic_quotes_gpc()) {
                    $input = addslashes($input);
                }
            }
        }
        //防止最后一个反斜杠引起SQL错误如 'abc\'
        if(substr($input,-1,1)=='\\') $input=$input."'";//$input=substr($input,0,strlen($input)-1);
        return $input;
    }
}