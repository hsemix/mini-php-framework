<?php
namespace App;
use DataFrame\Views\View;
use DataFrame\Database\DbSchema;
use DataFrame\Database\BluePrints;
class Home{
	public function __construct(){
		echo 'This is the home page';
		
	}
	public function index(){
		return new View("test",['name'=>'semix']);
		$user = User::find(1);
		$posts = Post::all();
		$book = Book::find(2);
		//$photo = \Photograph::find(2);
		//$test = DB::table('user')->get();
		$test = DbSchema::create("users", function(BluePrints $table){
			$table->increments('id');
			$table->string("name");
			//$table->engine = "InnoDB";
			$table->run();
		});
		echo '<pre>';
		//print_r($user->userGroups);
		print_r($test);
	}
}