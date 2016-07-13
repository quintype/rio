<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', 'HomeController@index');

Route::get('/ping', function () {
    return 'pong';
});

//Route::get('/preview/home', 'PreviewController@home');
//Route::get('/section/year/month/date/story_slug', 'PreviewController@home');

Route::get('/{section}/{year}/{month}/{date}/{story_slug}', [
    'uses' => 'HomeController@storyview'
]);

// Route::get('/section/inquiring-minds', [
//     'uses' => 'HomeController@podcastview'
// ]);

Route::get('/section/{section}', [
    'uses' => 'HomeController@sectionview'
]);


Route::get('/search', [

    'uses' => 'HomeController@searchview'
]);

Route::get('/topic/{tagname}', [

    'uses' => 'HomeController@tagsview'
]);

Route::get('/privacy-policy', [

    'uses' => 'HomeController@privacyview'
]);
Route::get('/about-us', [

    'uses' => 'HomeController@aboutview'
]);
Route::get('/terms-and-conditions', [

    'uses' => 'HomeController@termsview'
]);


Route::get('/Loadmore',function(){

  if(Request::ajax()){
    return "Ajax Successful";
  }
});





