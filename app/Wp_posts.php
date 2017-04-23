<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Wp_posts extends Model
{
	protected $connection = 'mysql_wp';
	protected $table = '';

	public function __construct(){
		$this->table = $_ENV["WP_POSTS"];
	}
}
