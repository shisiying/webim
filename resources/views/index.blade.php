<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <title>公共聊天室-基于swoole扩展php开发</title>
    <meta name="description" content="梦的起飞，公共聊天室，聊天室基于swoole扩展、PHP开发，。赶快加入，聊天、交友。">
    <meta name="keywords" content="聊天室，公共聊天室，梦的起飞，swoole，扩展，PHP开发">
    <link media="all" href="{{asset('css/style.css?v=2232')}}" type="text/css" rel="stylesheet">
    <link media="all" href="{{asset('css/shake.css?v=2222')}}" type="text/css" rel="stylesheet">
</head>
<body>
<div id="layout-container">
    <div id="qrcodeimg">
        <ul >
            <li style="margin-bottom: 30px"><img src="{{asset('images/sw.png')}}" alt="swoole群组" style="height: 80px;width: 80px;" title="微信扫码后可收到此群聊的信息"><p>sw群组</p></li>
            <li style=" margin-bottom: 30px"><img src="{{asset('images/php.png')}}" alt="php群组" style="height: 80px;width: 80px;" title="微信扫码后可收到此群聊的信息"><p>php群组</p></li>
            <li ><img src="{{asset('images/go.png')}}" alt="go群组" style="height: 80px;width: 80px;" title="微信扫码后可收到此群聊的信息"><p>go群组</p></li>
        </ul>
    </div>
    <div id="layout-main">
        <div id="header">
            <div style="float: right;margin: 17px 15px 4px 0px;">
                <div title="退出" class="iconfont" onclick="chat.logout()" style="height: 26px;width: 26px;color: #EDF7FF;font-size: 26px;line-height: 26px;cursor: pointer;"></div>
            </div>
            <div class="search-bar-wraper" style="width: 432px; text-align: center"><span class="chat-window-name"></span></div>
        </div>
        <div id="body">
            <div id="menu-pannel">
                <div class="profile"></div>
                <ul class="main-menus" id="main-menus"></ul>
            </div>
            <div id="menu-pannel-body">
                <div id="sub-menu-pannel" class="conv-list-pannel">
                    <div class="conv-lists-box" id="user-lists">
                        <div class="conv-lists" id="conv-lists"></div>
                    </div>
                </div>
                <div id="content-pannel">
                    <div class="conv-detail-pannel">
                        <div class="nocontent-logo" style="display:none;" >
                            <div>
                                <img alt="欢迎" src="{{asset('images/noimg.png')}}">
                            </div>
                        </div>
                        <div class="content-pannel-body chat-box-new" id="chat-box">
                            <div class="main-chat chat-items" id="chat-lists">
                                <div class="msg-items" id="chatLineHolder"></div>
                            </div>
                        </div>
                        <div>
                            <div class="send-msg-box-wrapper">
                                <div class="input-area" style="display:none;">
                                    <ul class="tool-bar">
                                        <li class="tool-item">
                                            <i class="iconfont tool-icon tipper-attached emotion_btn" title="表情"></i>
                                            <div class="faceDiv"></div>
                                        </li>
                                    </ul>
                                    <span class="user-guide">Enter 发送 , Ctrl+Enter 换行</span>
                                    <div class="msg-box" style="height:100%;">
                                        <textarea class="textarea input-msg-box" onkeydown="chat.keySend(event);" id="chattext"></textarea>
                                    </div>
                                </div>
                                <div class="action-area" style="display:none;">
                                    <a href="javascript:;" class="send-message-button" onclick="chat.sendMessage()">发送</a>
                                </div>
                                <div id="loginbox" class="area" style="width:100%;text-align:center;display:block;">
                                    <form action="javascript:void(0)" onsubmit="return chat.doLogin('','');">
                                        <div class="clearfix" style="margin-top:35px">
                                            <input name="name" id="name" style="margin-right:20px;width:250px;" placeholder="请输入昵称" class="fm-input" value="" type="text">
                                            <input id="email" class="fm-input" style="margin-right:20px;width:250px;" name="email" placeholder="请输入Email" type="text">
                                            <button type="submit" class="blue big">登录</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="carrousel"> <span class="close entypo-cancel"></span>
    <div class="wrapper"> <img src="{{asset('images/noimg.png')}}" /> </div>
</div>
<script src="{{asset('js/init.js')}}"></script>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/face.js?v=33458')}}"></script>
<script src="{{asset('js/create.div.js?v=17')}}"></script>
<script src="{{asset('js/chat.script.js?v=34')}}"></script>
<script src="{{asset('js/functions.js?v=2115')}}"></script>
<script src="{{asset('js/xlyjs.js?v=215')}}"></script>
<a href="https://github.com/shisiying/webim" target="_blank">
    <img style="position: absolute; top: 0; right: 0; border: 0; z-index:9999;" src="{{asset('images/forkme_right_orange_ff7600.png')}}" alt="Fork me on GitHub">
</a>
</body>
</html>

