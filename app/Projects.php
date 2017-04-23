<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
	public function scopeListings($query)
	{

		extract($_ENV);
		$P = $T_PROJECTS;


		return $query->select( "{$P}.id", "{$P}.name AS pr_name", "{$P}.live_url", "{$P}.client_id", "{$P}.client_type", "e.name AS client_name", "{$P}.date_start",
					"{$P}.date_completed", "{$P}.viewable", "{$P}.about_text", "{$P}.password_required", "{$P}.cpm_id", "pe.name AS cpm_name", "{$P}.ipm_id",
					"pe2.name AS ipm_name", "pp.file_name", "pp.live_url AS piece_live_url", "pp.display_order_override", "pp.thumb_file_override", "pp.thumb_Coords",
					"ppm.is_prime", DB::raw("GROUP_CONCAT(pttr.pttid) AS pttids") )
				->leftJoin( "{$T_ENTITIES} AS e", "{$T_PROJECTS}.client_id", '=', "e.id")
				->leftJoin( "{$T_PEOPLE} AS pe", "{$T_PROJECTS}.cpm_id", '=', "pe.current_primary_organization")
				->leftJoin( "{$T_PEOPLE} AS pe2", "pe2.id", '=', "{$P}.ipm_id")
				->leftJoin( "{$T_PROJECT_PIECES_RELATIONS} AS ppm", "ppm.pr_id", '=', "{$P}.id")
				->leftJoin( "{$T_PROJECT_PIECES} AS pp", "pp.id", '=', "ppm.pi_id")
				->leftJoin( "{$T_PROJECT_TYPE_TAGS_RELATIONS} AS pttr", "{$P}.id", '=', "pttr.pid")
				->where( [
					["{$P}.id", "<>", "0"],
					["{$P}.viewable", '<>', "0"],
					["pp.file_name", '<>', ""]
				] )
				->orderBy("pp.display_order_override", 'asc')
				->orderBy("{$P}.date_completed", 'desc')
				->orderBy("ppm.is_prime", 'desc')
				->groupBy("{$P}.id");
	}
}
