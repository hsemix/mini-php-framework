<?php
namespace App;
use DataFrame\Views\View;
class About{
	public function __construct(){
		echo 'This is the about page';

	}

	public function intro($id){
		//echo $id .' is '.$name;

		$posts = Post::all();

		return new View('posts', array('posts'=> $posts), 'html');
	}
	public function index($id){
		$posts = Post::where("id", "<", $id)->get;

		return new View('posts', array('posts'=> $posts), 'html');
	}

}