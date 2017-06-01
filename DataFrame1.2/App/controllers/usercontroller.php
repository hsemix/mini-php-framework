<?php
namespace App;
use App\User;
use DataFrame\Token;
use DataFrame\Request;
use DataFrame\Response;
use DataFrame\Validate;
class UserController{
	public function passwordReset(Request $request, Response $response, User $user, Token $token, Validate $validate){
		if($request->exists()){
			if($token->check($request->get(getGlobals('token.token_name')))){
				$validation = $validate->check($_POST, [
					'email|Email Address' => [
						'required' => true
					]
				]);

				if($validation->passed()){
					$email = $request->get('email');
					if($user->where('email', $email)->first()){
						$thisUser = $user->where('email', $email)->first();
						$password = substr(csrf_token(), 0,6);
						$headers = "From: Musawo <noreply@musawoapp.com>".PHP_EOL;
				        $headers .= "Reply-To: Musawo ";
				        $headers .= "<info@musawoapp.com>".PHP_EOL.'X-Mailer: PHP/' . phpversion();
				        $headers .= "MIME-Version: 1.0\r\n";
				        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				        $subject = "Your new password";
				        $message = "Your new MusawoApp Password is\n\n Password: ".$password;
				        @mail($email, $subject, $message, $headers);
				        $thisUser->password = $password;
				        $thisUser->save();
						$msgs = [
		                	'success' => "Your new Passwor has been sent to your email ({$email})",
		                    'token' => csrf_token()
		               	];
		            	$response->json(['responseText' => $msgs], 300);
					}else{
						$msgs = [
		                	'errors' => 'Unknown Email Address',
		                    'token' => csrf_token()
		               	];
		            	$response->json(['responseText' => $msgs], 1000);
					}
				}else{
					$msgs = [
	                	'errors' => implode("<br />",$validation->errors()),
	                    'token' => csrf_token()
	               	];
	            	$response->json(['responseText' => $msgs], 1000);
				}
			}
		}
	}
}