<?php
namespace App;
use DataFrame\Controllers\Controller;
class HomeController extends Controller{
	private $users;
	private $feedback;
	private $appointments;
	public function __construct(User $users, Appointment $appointments, Feedback $feedback){
		$this->users = $users;
		$this->feedback = $feedback;
		$this->appointments = $appointments;
		//$this->middleware('loggedIn');
		parent::__construct();
	}
	public function index(){
		$data = [
			'users' => $this->users->all(),
			'appointments' => $this->appointments->orderBy('created_at DESC')->all(),
			'feedback' => $this->feedback->orderBy('created_at DESC')->all(),
			'user' => $this->users->find($this->login_id),
			'chats' => Message::getDocs()
		];
		$app = $this->appointments->with('comments.posts');

		//echo '<pre>';

		//print_r($app);
		
		return view('index', $data);
	}

	public function messenger(){
		$data = [
			'docs' => $this->users->where('type_id', 2)->get(),
			'chats' => Message::getDocs()
		];


		return view('messenger', $data);
	}
	public function userLogout(){
        if($this->session->exists(getGlobals("session.session_name"))){
            $this->session->delete(getGlobals("session.session_name"));
            $this->session->logout();
            $this->res->redirectTo("/login");
        }
    
	}
}