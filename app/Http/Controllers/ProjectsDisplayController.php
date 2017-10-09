<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProjectsDisplayController extends Controller
{
     
	protected $resultsPerPage = 10;

	public function projectsReturn(){

		//prime query
		$projects = \App\Projects::listings()
		->paginate( $this->resultsPerPage );

		return response()->json( $projects );
	}

	public function projectsByDeveloperId($did){

		$projects = \App\Projects::listings()
		->where("pe2.id", "=", $did)
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}

	public function projectsByDeveloperName($dName){

		$projects = \App\Projects::listings()
		->where("pe2.name", "LIKE", "%{$dName}%")
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}

	public function projectsByClientId($cId){

		$projects = \App\Projects::listings()
		->where( "e.id", "=", "%{$cId}%" )
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}

	public function projectsByClientName($cName){

		$projects = \App\Projects::listings()
		->where( "e.name", "LIKE", "%{$cName}%" )
		->paginate( $this->resultsPerPage );

		return response()->json($projects);
	}

	protected function projectBy($prRef, $type="id"){
		$P = $_ENV["T_PROJECTS"];
		$T_PROJECT_PIECES_RELATIONS = $_ENV["T_PROJECT_PIECES_RELATIONS"];
		$T_PROJECT_PIECES = $_ENV["T_PROJECT_PIECES"];

		$whereArr = ["{$P}.id", "=", $prRef];

		if($type !== "id"){
			$whereArr = ["{$P}.name", "LIKE", "%{$prRef}%"];
		}


		$project = \App\Projects::listings()
		->addSelect( DB::raw("GROUP_CONCAT(DISTINCT(pp2.id)) AS ppids") )
		->leftJoin( "{$T_PROJECT_PIECES_RELATIONS} AS ppm2", "ppm2.pr_id", '=', "{$P}.id")
		->leftJoin("{$T_PROJECT_PIECES} AS pp2", "pp2.id", '=', "ppm2.pi_id")
		->where( [
			$whereArr
		] )
		->first();

		return response()->json($project);
	}

	public function projectById($prid){
		return $this->projectBy($prid, "id");
	}

	public function projectByName($prName){
		return $this->projectBy($prName, "name");
	}


	public function beforeAfter($which=''){
		$T_PROJECT_PIECES = $_ENV["T_PROJECT_PIECES"];
		$T_PROJECT_META = $_ENV["T_PROJECT_META"];

		$whereArr = array();
		$whereArr[0] = ["pm.meta_key", "=", "before_after_pieces"];

		$which = trim($which);


		/*
SELECT pp.id, pp.file_name, pp.thumb_file_override, pp.thumb_Coords, pp.live_url, pp.name, pm.proj_id
FROM `project_pieces` pp, `project_meta` pm 
WHERE pp.id IN (126,135)
AND pm.meta_key = "before_after_pieces"
		*/

		if( strlen($which) > 2 && preg_match("/^[0-9]+\,+[0-9]+\,+[0-9]+$/", $which) ){
			list($projId, $beforeAfter) = explode(",", $which, 2);

			/*$whereArr[1] = ["pm.proj_id", "=", $projId];
			$whereArr[1] = ["pm.meta_value", "=", '"'. $beforeAfter . '"'];*/

			
			$results = DB::select( DB::raw("SELECT pp.id, pp.file_name, pp.thumb_file_override, pp.thumb_Coords, pp.live_url, pp.name, pm.proj_id
			 FROM {$T_PROJECT_PIECES} AS pp, {$T_PROJECT_META} AS pm
			 WHERE pp.id IN ({$beforeAfter})
			 AND pm.meta_key = 'before_after_pieces'") );

			return response()->json( $results );
		} else {
			return 0;
		}

		/*$projectTags = DB::table( "{$T_PROJECT_PIECES} AS pp, {$T_PROJECT_META} AS pm" )
		->select( "pp.id", "pp.file_name", "pp.thumb_file_override", "pp.thumb_Coords", "pp.live_url", "pp.name", "pm.proj_id" )
		->where( $whereArr )
		->paginate( $this->resultsPerPage );*/

		/*$results = DB::select( DB::raw("SELECT * FROM some_table WHERE some_col = '$someVariable'") );

		return response()->json($projectTags);*/

		//var_dump( dd(DB::getQueryLog()) );

		//return 0;
	}

	public function caseStudies( $prId=0 ){

		//defaults
		$pickIdConditional = "1 = 1";
		$contentSelect = "CONCAT(SUBSTR(post_content, 1, 256), '...') AS c";

		//whether or not this is for a single project id
		if( is_numeric($prId) && $prId > 0 ){
			$pickIdConditional = "proj_id = ". $prId;

			$contentSelect = "post_content AS c";
		}

		$case_study_relations = DB::table( $_ENV["T_PROJECT_META"] )
		->select( "proj_id AS p", "meta_value AS w" )
		->where("meta_key", "=", "_wp_case_study_postid" )
		->whereRaw( $pickIdConditional )
		->paginate( $this->resultsPerPage );


		if( count($case_study_relations) > 0 ){
			$csrArr = array();
			for( $i=0; $i<count( $case_study_relations ); $i++ ){
				$csrArr[ $case_study_relations[$i]->w ] = $case_study_relations[$i]->p;
			}

			//a string to be fed by query below
			$csrStr = implode(",", array_keys($csrArr) );

			//getting things from the wordpress database
			$posts = DB::connection('mysql_wp')->table( $_ENV["WP_POSTS"] )
			->select( "ID AS id", DB::raw($contentSelect) )
			->whereIn('ID', explode( ",", $csrStr ) )
			->where("post_status", "=", "publish")
			->get();

			for( $i=0; $i<count( $posts ); $i++ ){
				$posts[$i]->pr = $csrArr[ $posts[$i]->id ];
			}

			return response()->json($posts);
		} else {
			return "";
		}
	}

	public function allProjectTags(){
		$projectTags = DB::table( $_ENV["T_PROJECT_TYPE_TAGS"] )->select( DB::raw( 'GROUP_CONCAT("\"", id, "\"", ": ", "\"", name, "\"") AS g' ) )->paginate( 500 );

		//manual construction of string
		$tempJstr = "{" . $projectTags[0]->g . "}";

		return response($tempJstr, 200)->header('Content-Type', 'application/json');
	}
	
}
