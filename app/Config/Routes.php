<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');


//$routes->get("get-property/(:num)", "Property::show/$1");
 
$routes->group("property", ["filter" => "basicauthFilter"] , function($routes){

   $routes->post("add", "Property::add_prop");
   $routes->get("prop/(:num)", "Property::show/$1");
   $routes->post("prop/(:num)", "Property::show/$1"); 
   $routes->get("prop", "Property::index");
   $routes->get("home_prop", "Property::get_home_page_prop");
   $routes->get("prop_category/(:any)", "Property::property_by_category/$1");
      
   
     
    // $routes->get("prop", "Home::index");     
     //$routes->post("add-employee", "ApiController::addEmployee");
});

$routes->group("login", ["filter" => "basicauthFilter"] , function($routes){
   $routes->post("user", "Login::index");
   $routes->get("user", "Login::index"); 
 });

$routes->group("otp", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('otp/(:num)', 'Otp::generate_otp/$1');
      $routes->post('otp/(:num)', 'Otp::generate_otp/$1');
 });
$routes->group("otp_auth", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('auth/(:num)', 'Otp_auth::auth/$1');
      $routes->post('auth/(:num)', 'Otp_auth::auth/$1');
 });
$routes->group("new_contact_us", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('data_insert', 'Contact_us::insert_data');
      $routes->post('data_insert', 'Contact_us::insert_data');
 });

$routes->group("profile_check", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('fetch', 'User_profile_check::check');
      $routes->post('fetch', 'User_profile_check::check');
 });
$routes->group("profile_set", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('set', 'Profile_update::put');
      $routes->post('set', 'Profile_update::put');
       $routes->get('get', 'Profile_update::fetch');
      $routes->post('get', 'Profile_update::fetch');
 });

$routes->group("social_profile_check", ["filter" => "basicauthFilter"] , function($routes){
      $routes->get('check', 'User_profile_check::fetch');
      $routes->post('check', 'User_profile_check::fetch');
 });


 $routes->group("Search", ["filter" => "basicauthFilter"] , function($routes){
    // $routes->post("user", "Login::index");
    $routes->get("query", "Search::index"); 
  });


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
