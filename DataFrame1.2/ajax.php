<?php
use DataFrame\Session;
include_once('header.php');

if(isset($_REQUEST['security_token'])){
    $security_token = $_REQUEST['security_token'];
	if($security_token =='js_user_login' || $security_token == 'core.user_login'){
    }
}elseif(isset($_REQUEST['mGlobals'])){
	$security_token = $_REQUEST['mGlobals']['call'];
    $date = new DateTime('now', new DateTimeZone('UTC'));
    if(Session::exists(getGlobals("session.session_name"))){
        $userId = Session::get(getGlobals('session.session_name'));
        $user = \App\User::find($userId);
    }
	if($security_token == 'confirm.appointment'){
		$id = request()->get('id');

		$appointment = App\Appointment::find($id);
		$appointment->status = 1;
		$appointment->save();
		response()->json(['responseText' => 'Confirmed']);
	}elseif($security_token == 'fetch.chat.messages'){
		$messages = App\Message::where('chat_id', request()->get('id'))->orderBy('created_at ASC')->all();
		$html = '';
		foreach($messages as $message){
			$message->status = 1;
			$message->save();
			$msgdate = (new DateTime($message->created_at, new DateTimeZone('Africa/Kampala')))->format('jS M Y ga');
			$html .= '<div class="direct-chat-msg ';

			if($message->sender->id == request()->get('user')){
				$html .= 'right';
			}

			$html .= '">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name ';

                        if($message->sender->id == request()->get('user')){
                        	$html .= 'pull-right';
                        }else{
                        	$html .= 'pull-left';
                        }

                        $html .= '">'.$message->sender->fullname.'</span>
                        <span class="direct-chat-timestamp ';
                        if($message->sender->id == request()->get('user')){
                        	$html .= 'pull-left';
                        }else{
                        	$html .= 'pull-right';
                        }
                        $html .= '">'.$msgdate.'</span>
                      </div>
                      <img class="direct-chat-img" src="/'.response()->getOrSetVars()->resource.'dist/img/user1-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                        '.$message->content.'
                      </div>
                    </div>';
		}
		response()->json(['responseText' => $html, 'doc'=>App\User::find(request()->get('user'))->fullname]);
	}elseif($security_token == 'check.new.messages'){
		$messages = App\Message::checkForNew()->where('status', '0');
		response()->json(['responseText' => $messages->count()]);
	}elseif($security_token == 'read.new.messages'){
		$messages = App\Message::checkForNew();
		$html = '';
		foreach($messages as $message){
			$msgdate = (new DateTime($message->created_at, new DateTimeZone('Africa/Kampala')))->format('jS M Y ga');
			//$m = App\Message::find($message->id);
			//$m->status = 1;
			//$m->save();

			$html .= '<div class="box-comment" data-doc="'.$message->reciever->id.'" data-chat="'.$message->chat_id.'" onclick="$musawo.websitechat.showthechat(this);" style="cursor:pointer;">
                <!-- User image -->
                <img class="img-circle img-sm" src="/'.response()->getOrSetVars()->resource.'dist/img/user3-128x128.jpg" alt="User Image">

                <div class="comment-text">
                      <span class="username">
                        '.$message->sender->fullname.'
                        <span class="text-muted pull-right">'.$msgdate.'</span>
                      </span><!-- /.username -->
                      <span class="chat-recent-text">
                      '.$message->content.'
                    </span> 
                    <span>
                      <b>To:</b> '.$message->reciever->fullname;
                      if($message->status == 0){
                        $html .= '<i class="text-muted label-success new-messages  pull-right"> New </i>';
                      }
                    $html .= '</span>
                </div>
                <!-- /.comment-text -->
              </div>';
		}

		response()->json(['responseText' => $html]);
	}
}