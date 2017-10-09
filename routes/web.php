<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return 'Please use valid endpoint';
});

Route::group(array('prefix' => 'projects'), function()
{

	$ctrlName = 'ProjectsDisplayController';

	$numPattern = '^[0-9]+$';
	$namePattern = '[A-Za-z]+[A-Za-z\-\_\s]{0,}';

	//show all projects. 'page' is for pagination
	Route::get('/', "{$ctrlName}@projectsReturn");
	
	// First Route, show projects by author, numerical
	Route::get('/developer/{dId}', "{$ctrlName}@projectsByDeveloperId")->where('dId', $numPattern);

	//name based
	Route::get('/developer/{dName}', "{$ctrlName}@projectsByDeveloperName")->where('dName', $namePattern);


	//projects by client
	Route::get('/client/{cId}', "{$ctrlName}@projectsByClientId")->where('cId', $numPattern);

	Route::get('/client/{cName}', "{$ctrlName}@projectsByClientName")->where('cName', $namePattern);


	// before and after, numerical array of projects with each member having both a before and after screenshot
	Route::get('/before-after/{which?}', "{$ctrlName}@beforeAfter");

	// get the project tags
	Route::get('/project-tags', "{$ctrlName}@allProjectTags");


	//show that particular project, some cms stuff plus an array of pieces
	Route::get('/{prid}', "{$ctrlName}@projectById")->where('prid', '^[0-9]+$');

	//same as above, but name based instead
	Route::get('/{prName}', "{$ctrlName}@projectByName")->where('prName', '[A-Za-z]+');
});


// case studies, specific solutions for businesses
Route::get('/case-studies/{prid?}', "ProjectsDisplayController@caseStudies");



Route::group(array('prefix' => 'services'), function()
{

    // just 'services' with nothing else
    Route::get('/', function() {
        //return 'Reaper Man';

        $pages = \App\Wp_posts::where("ID", "=", "41")
        ->paginate( 9 );

        return response()->json( $pages );
    });

    Route::group(array('prefix' => 'web'), function()
    {
        // just 'services/web' with nothing else
        Route::get('/', function() {
            return 'Reaper Man';
        });

        // responsive and mobile sites
        Route::get('/responsive-mobile', function() {
            return 'Reaper Man';
        });

        // wordpress sites
        Route::get('/wordpress', function() {
            return 'Reaper Man';
        });

        // magento sites
        Route::get('/magento', function() {
            return 'Reaper Man';
        });

        // single page apps
        Route::get('/single-page-apps', function() {
            return 'Reaper Man';
        });

        // email design
        Route::get('/email-design', function() {
            return 'Reaper Man';
        });

        // site upgrade / repair
        Route::get('/site-upgrade-repair', function() {
            return 'Reaper Man';
        });
    });

    Route::group(array('prefix' => 'creative'), function()
    {
        
    });

});

Route::get('blog', function() {

    $page = \App\Wp_posts::where("ID", "=", "42")
    ->first();

    return response()->json( $page );
});

Route::get('about', function() {

    $page = \App\Wp_posts::where("ID", "=", "42")
    ->first();

    return response()->json( $page );
});