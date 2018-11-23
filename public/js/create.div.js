/*创建div dom*/
var cdiv = {
	userlists : function(roomid,is_none){
		var arr = [];
		arr = [ '<div class="conv-lists" id="conv-lists-',roomid,'" style="display:',is_none,'"></div>'];
		return arr.join('');
	},
	chatlists : function(roomid,is_none){
		var arr = [];
		arr = [ '<div class="msg-items" id="chatLineHolder-'+roomid+'" style="display:',is_none,'"></div>'];
		return arr.join('');
	},
	render : function(template,params){
		var arr = [];
		switch(template){
			case 'mymessage':
				arr = [
					'<div style="display: block;" class="msg-box"><div class="chat-item me"><div class="clearfix"><div class="avatar"><div class="normal user-avatar" style="background-image: url(',params.avatar,');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div><div class="msg-bubble"><pre class="text">', params.newmessage,'</pre></div></div></div></div></div></div></div>'
				];
			break;
            case 'mySecretMessage':
                arr = [
                    '<div style="display: block;" class="msg-box"><div class="chat-item me"><div class="clearfix"><div class="avatar"><div class="normal user-avatar" style="background-image: url(',params.send_avatar,');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div><div class="msg-bubble"><pre class="text">', params.newmessage,'</pre></div></div></div></div></div></div></div>'
                ];
                break;
			case 'chatLine':
				arr = [
                    '<div style="display: block;" class="msg-box"><div class="chat-item not-me"><div class="chat-profile-info clearfix"><span class="profile-wrp"><span class="name clearfix"><span class="name-text">',params.name,'</span></span></span><span class="chat-time">',params.time,'</span></div><div class="clearfix"><div class="avatar"><div class="normal user-avatar" onclick="chat.remind(this)" fd="',params.fd,'" uname="',params.name,'" style="background-image: url(\'',params.avatar,'\');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div class="msg-bubble"><pre class="text">',params.newmessage,'</pre></div></div></div></div></div></div>'
				];
			break;

            case 'secretChatLine':
                arr = [
                    '<div style="display: block;" class="msg-box"><div class="chat-item not-me"><div class="chat-profile-info clearfix"><span class="profile-wrp"><span class="name clearfix"><span class="name-text">',params.send_name,'</span></span></span><span class="chat-time">',params.time,'</span></div><div class="clearfix"><div class="avatar"><div class="normal user-avatar" onclick="chat.remind(this)" fd="',params.send_fd,'" uname="',params.send_name,'" style="background-image: url(\'',params.send_avatar,'\');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div class="msg-bubble"><pre class="text">',params.newmessage,'</pre></div></div></div></div></div></div>'
                ];
                break;
			
			case 'user':
				arr = [
					'<div id=\'user-',params.fd,'\' onclick="chat.remind(this)" avatar="',params.avatar,'" fd="',params.fd,'" uname="',params.name,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="avatar-wrap"><div class="group-avatar"><div class="normal group-logo-avatar" style="background-image: url(',params.avatar,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.name,'</p></div><span class="time">',params.time,'</span></div></div></div>'
				];
			break;
			case 'newlogin':
				arr = [
					'<div class="chat-status chat-system-notice">系统消息：欢迎&nbsp;',params.name,'&nbsp;加入群聊</div>'
				];
				break;
			case 'logout':
				arr = [
					'<div class="chat-status chat-system-notice">系统消息：&nbsp;',params.name,'&nbsp;退出了群聊</div>'
				];
				break;
			case 'my':
				arr = [
					'<div class="big-52 with-border user-avatar me" uid="',params.fd,'" title="',params.name,'" style="background-image: url(',params.avatar,');"></div><p>',params.name,'</p>'
				];
				break;
            case 'secretChat':
                arr = [
                    '<div class="profile"  onclick="chat.remind(this)" fd="',params.fd,'" uname="',params.name,'" avatar="',params.avatar,'" id=\'secretChatUser-',params.fd,'\'><div class="big-52 with-border user-avatar" uid="',params.fd,'"  title="',params.name,'" style="background-image: url(',params.avatar,');" ></div><p>',params.name,'</p></div>'
                ];
                break;
            case 'secretChatSend':
                arr = [
                    '<div class="profile"  onclick="chat.remind(this)" fd="',params.send_fd,'" uname="',params.send_name,'" avatar="',params.send_avatar,'" id=\'secretChatUser-',params.send_fd,'\'><div class="big-52 with-border user-avatar" uid="',params.send_fd,'"  title="',params.send_name,'" style="background-image: url(',params.send_avatar,');" ></div><p>',params.send_name,'</p></div>'
                ];
                break;
			case 'rooms':
				arr = [
					'<li class="menu-item ',params.selected,'"  roomname=',params.roomname,'  roomid="',params.roomid,'" onclick="chat.changeRoom(this)">',params.roomname,'<span id="message-',params.roomid,'">0</span></li>'
				];
				break;
		}
		return arr.join('');
	}
	
}
