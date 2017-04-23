<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProjectsDisplayController extends Controller
{
     
	protected $resultsPerPage = 10;

	public function responsiveMobile(){

		//prime query
		$pages = \App\Wp_posts::where("1 = 1")
		->paginate( $this->resultsPerPage );

		return response()->json( $pages );
	}

	public function projectsByDeveloperId($did){

		$projects = \App\Projects::listings()
		->where("pe2.id", "=", $aid)
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}

	public function projectsByDeveloperName($dName){

		$projects = \App\Projects::listings()
		->where("pe2.name", "LIKE", "%{$aName}%")
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}
	
}
