 <?php

use Illuminate\Routing\middleware;
use App\Http\Middleware\Checkk;
use App\Http\Middleware\CheckAdminOrg;
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

Route::get('/', 'ClientPanelController@welcome')->name('home');




///////////////////////////////////// USER

Route::get('/registration', 'RegistrationController@form');
Route::post('/register', 'RegistrationController@register');
Route::get('/registration/verify/{confirmation_code}','RegistrationController@verify');


Route::get('/login', 'LoginController@form');
Route::post('/login', 'LoginController@login');
Route::get('/logout','LoginController@destroy');

Route::get('/edit_profile','ClientPanelController@show_edit_profile');
Route::post('/edit_profile','ClientPanelController@edit_profile');

Route::post('/reserve_cart', 'ReservationController@cart_to_reservation');

Route::get('/reservation_options','ReservationController@reservation_options');

Route::get('/solo_reservation/{name}','ReservationController@reservation');
Route::get('/solo_reservation/{name}/{date}','ReservationController@ajax_reservation');
Route::post('/solo_reservation/reserve','ReservationController@add_to_cart_solo');


Route::get('/group_reservation/check_day/{name}/{field_name}','ReservationController@ajax_check_days');
Route::get('/group_reservation/{name}','ReservationController@group_reservation');
Route::get('/group_reservation/search/{name}','ReservationController@group_reservation_search');
Route::post('/group_reservation/reserve','ReservationController@add_to_cart_group');

Route::get('/room_reservation','ReservationController@room_reservation');
Route::get('/room_reservation/search','ReservationController@room_reservation_search');
Route::post('/room_reservation/reserve','ReservationController@add_to_cart_hotel');


Route::get('/reservations_in_cart','ClientPanelController@reservations_in_cart');
Route::get('/reservations_in_cart/remove/{user_id}/{id}/{object_type}','ClientPanelController@remove_reservations_in_cart');
Route::get('/reservations_in_cart/remove_cart/{user_id}', 'ClientPanelController@remove_cart');

Route::get('/my_reservations', 'ClientPanelController@show_my_reservations');
Route::post('/my_reservations/remove/', 'ClientPanelController@remove_my_reservation');
Route::get('/my_reservations/solo/{name}/{reservation_id}', 'ClientPanelController@show_my_reservations_solo');

Route::get('/my_reservations/hotel/{reservation_id}', 'ClientPanelController@show_my_reservations_hotel');
Route::get('/my_reservations/group/{name}/{reservation_id}', 'ClientPanelController@show_my_reservations_group');



Route::group(['middleware' => ['checkk:admin']], function () {


Route::get('/new_object', 'RegistrationController@new_object_form');
Route::post('/new_object', 'RegistrationController@new_object');
Route::get('/new_field/{name}', 'AdminController@new_field_form');
Route::post('/new_field', 'AdminController@new_field');
Route::get('/new_room', 'AdminController@new_room_form');
Route::post('/new_room', 'AdminController@new_room');

Route::post('/group_edit_time', 'AdminController@group_edit_time');
Route::post('/group_add_day', 'AdminController@group_add_day');
Route::post('/group_edit_day_time', 'AdminController@group_edit_day_time');
Route::post('/group_remove_day', 'AdminController@group_remove_day');
Route::post('/group_edit_break_time', 'AdminController@group_edit_break_time');
Route::post('/group_edit_field', 'AdminController@group_edit_field');
Route::post('/group_remove_field', 'AdminController@group_remove_field');
Route::post('/object/group/remove', 'AdminController@object_group_remove');


Route::post('/solo_edit_time', 'AdminController@solo_edit_time');
Route::post('/solo_add_day', 'AdminController@solo_add_day');
Route::post('/solo_edit_day_time', 'AdminController@solo_edit_day_time');
Route::post('/solo_remove_day', 'AdminController@solo_remove_day');
Route::post('/solo_edit_break_time', 'AdminController@solo_edit_break_time');
Route::post('/solo_edit_maxguests', 'AdminController@solo_edit_maxguests');
Route::post('/solo_edit_cost', 'AdminController@solo_edit_cost');
Route::post('/object/solo/remove', 'AdminController@object_solo_remove');


Route::post('/hotel_edit_room', 'AdminController@hotel_edit_room');
Route::post('/hotel_remove_room', 'AdminController@hotel_remove_room');



});




///////////////////////////////////// USER





////////////////////////////////////ADMIN i organizator też
Route::group(['middleware' => ['checkadminorg:organizator,admin']], function () {
Route::get('/users_list','AdminController@show_users');
Route::get('/users_list/remove/{user_id}','AdminController@delete_user');
Route::get('/users_list/edit/{user_id}','AdminController@render_view_edit_user');
Route::post('/users_list/edit/{user_id}','AdminController@edit_user');


Route::get('/users_list/active_reservations/solo/{name}/{user_id}','AdminController@show_active_solo_reservations');
Route::get('/users_list/active_reservations/solo/{name}/remove/{user_id}/{reservation_id}/{reservation_start}','AdminController@delete_solo_active_reservations');
Route::get('/users_list/active_reservations/solo/remove/{name}/{user_id}/{reservation_id}','AdminController@delete_all_solo_active_reservations');
Route::get('/users_list/active_reservations/group/{name}/{user_id}','AdminController@show_active_group_reservations');
Route::get('/users_list/active_reservations/hotel/{user_id}','AdminController@show_active_hotel_reservations');


Route::get('/users_list/inactive_reservations/solo/{name}/{user_id}','AdminController@show_inactive_solo_reservations');
Route::get('/users_list/inactive_reservations/solo/{name}/remove/{user_id}/{reservation_id}/{reservation_start}','AdminController@delete_solo_inactive_reservations');
Route::get('/users_list/inactive_reservations/solo/remove/{name}/{user_id}/{reservation_id}','AdminController@delete_all_solo_inactive_reservations');


Route::get('/users_list/inactive_reservations/group/{name}/{user_id}','AdminController@show_inactive_group_reservations');
Route::get('/users_list/inactive_reservations/group/{name}/remove/{user_id}/{reservation_id}/{reservation_start}','AdminController@delete_group_inactive_reservations');
Route::get('/users_list/inactive_reservations/group/remove/{name}/{user_id}/{reservation_id}','AdminController@delete_all_group_inactive_reservations');


Route::get('/users_list/inactive_reservations/hotel/{user_id}','AdminController@show_inactive_hotel_reservations');
Route::get('/users_list/inactive_reservations/hotel/remove/{user_id}/{reservation_id}/','AdminController@delete_all_hotel_inactive_reservations');
Route::get('/users_list/inactive_reservations/hotel/remove/{user_id}/{reservation_id}/{reservation_start}','AdminController@delete_hotel_inactive_reservations');
// Route::get('/users_list/active_reservations/new_room_reservation/{user_id}','AdminController@show_new_room_reservation');
// Route::get('/users_list/active_reservations/new_room_reservation/search/{user_id}','AdminController@new_room_reservation_search');
// Route::post('/users_list/active_reservations/new_room_reservation/reserve/{user_id}/{date_start}/{date_end}/{guests_amount}/','AdminController@new_room_reservation_reserve');

Route::post('/reservation_activate','AdminController@reservation_activate');

Route::get('/admin_registration', 'RegistrationController@admin_register_form');
Route::post('/admin_register', 'RegistrationController@admin_register');

Route::get('/objects_list', 'AdminController@objects_list');
Route::get('/object/solo/{name}', 'AdminController@object_solo');
Route::get('/object/group/{name}', 'AdminController@object_group');
Route::get('/object/hotel/', 'AdminController@object_hotel');


Route::post('/close_solo_object', 'AdminController@close_solo_object');
Route::post('/close_group_object', 'AdminController@close_group_object');
Route::post('/close_room', 'AdminController@close_room');
});
////////////////////////////////////ADMIN








////////////////////////////////////ADMIN




// TEST KOSZYK


