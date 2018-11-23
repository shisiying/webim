<?php
/**
 * Created by PhpStorm.
 * User: seven
 * Date: 2018/11/17
 * Time: 17:07
 */
namespace App\Services;

class ChatUsersService
{
    protected $fd=0,$name = '', $avatar = '',$email='',$roomid='a';

    public function __construct(array $options = array())
    {
        if(!empty($options)){
            foreach($options as $k=>$v){
                if(isset($this->$k)){
                    $this->$k = $v;
                }
            }
        }
    }

    /**
     * @return array|mixed
     * swoole_table版本
     */
    public function getOnlineUsers(){
        $rooms = config('chat.rooms');
        $online_users = array();
        foreach($rooms as $_k => $_v){
            $infos = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$_k);
            if ($infos){
                $online_users[$_k] =json_decode($infos['infos'],true);
            }else{
                $online_users[$_k] =[];
            }
        }
        return $online_users;
    }


    /**
     * @return bool|int
     * swoole_table版本
     */
    public function save(){

        $userInfo = ['roomid'=>$this->roomid,'fd'=>$this->fd,'name'=>$this->name,'avatar'=>$this->avatar,'email'=>$this->email,'time'=>date("H:i",time())];
        //插入到room表，给房间增加一个用户
        $userArr = app('swoole')->ws_roomsTable->get($this->roomid);
        if ($userArr){
            $userArr = json_decode($userArr['users'],true);
            array_push($userArr,$this->fd);
        }else{
            $userArr = [$this->fd];
        }
        app('swoole')->ws_roomsTable->set($this->roomid, ['users' => json_encode($userArr)]);

        //插入到用户信息表
        app('swoole')->ws_usersTable->set('user'.$this->fd,$userInfo);

        //插入到房间用户信息表
        $roomUsersArr = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$this->roomid);
        if ($roomUsersArr){
            $roomUsersArr = json_decode($roomUsersArr['infos'],true);
            array_push($roomUsersArr,$userInfo);
        }else{
            $roomUsersArr = [$userInfo];
        }
        app('swoole')->ws_roomUsersTable->set('roomUsersInfo'.$this->roomid,['infos'=>json_encode($roomUsersArr)]);

        return true;
    }

    /**
     * @param $oldroomid
     * @param $fd
     * @param $newroomid
     * @return bool
     * swoole_table版本
     */
    public function changeUser($oldroomid,$fd,$newroomid){

        //更改用户的roomid
        $user = app('swoole')->ws_usersTable->get('user'.$fd);
        if ($user){
            $userInfo = [
                'roomid'=>$newroomid,
                'fd'=>$user['fd'],
                'name'=>$user['name'],
                'avatar'=>$user['avatar'],
                'email'=>$user['email'],
                'time'=>date("H:i",time())
            ];

            app('swoole')->ws_usersTable->set('user'.$fd,$userInfo);
        }

        //新房间里新增用户
        $userArrnew = app('swoole')->ws_roomsTable->get($newroomid);
        if ($userArrnew){
            $userArrnew = json_decode($userArrnew['users'],true);
            array_push($userArrnew,$fd);
        }else{
            $userArrnew = [$fd];
        }
        app('swoole')->ws_roomsTable->set($newroomid, ['users' => json_encode($userArrnew)]);


        //从老房间里删除用户
        $userArr = app('swoole')->ws_roomsTable->get($oldroomid);
        if ($userArr){
            $userArr = json_decode($userArr['users'],true);
            $key=array_search($fd,$userArr);
            array_splice($userArr,$key,1);
            app('swoole')->ws_roomsTable->set($oldroomid, ['users' => json_encode($userArr)]);
        }


        //插入到房间用户信息表
        $userInfo = ['roomid'=>$newroomid,'fd'=>$fd,'name'=>$this->name,'avatar'=>$this->avatar,'email'=>$this->email,'time'=>date("H:i",time())];

        $roomUsersArr = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$newroomid);
        if ($roomUsersArr){
            $roomUsersArr = json_decode($roomUsersArr['infos'],true);
            array_push($roomUsersArr,$userInfo);
        }else{
            $roomUsersArr = [$userInfo];
        }
        app('swoole')->ws_roomUsersTable->set('roomUsersInfo'.$newroomid,['infos'=>json_encode($roomUsersArr)]);


        //从房间用户信息删除
        $infos = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$oldroomid);
        if ($infos){
            $infos = json_decode($infos['infos'],true);
            if (!empty($infos)){
                foreach ($infos as $info_key => $row){
                    if ($row['fd']==$fd){
                        array_splice($infos,$info_key,1);
                        break;
                    }
                }
                app('swoole')->ws_roomUsersTable->set('roomUsersInfo'.$oldroomid,['infos'=>json_encode($infos)]);
            }
        }



        return true;
    }

    public function getUsersByRoom($roomid)
    {
        $infos = app('swoole')->ws_roomUsersTable->get('roomUsersInfo'.$roomid);
        if ($infos){
            $users =json_decode($infos['infos'],true);
        }else{
            $users =[];
        }
        return $users;
    }
}