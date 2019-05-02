<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['default_controller']             = "";

$route['admin']     					 = "superadmin";
$route['admin-profile'] 			 	 = "superadmin/profile"; 
$route['admin-logout'] 			 	 	 = "superadmin/logout"; 
$route['admin-change-password']     	 = "superadmin/change_password";
$route['admin-forgot-password']     	 = "superadmin/forgot_password";

$route['admin-dashboard'] 			 	 = "dashboard/index";


$route['admin-users']     	 			 = "user/index";
$route['admin-users/(:num)']     	 	 = "user/index/$1";
$route['admin-add-users']     	 		 = "user/addUser";
$route['admin-edit-users/(:num)']     	 = "user/editusers/$1";


$route['admin-pantry']     	 			 = "pantry/index";
$route['admin-pantry/(:num)']     	 	 = "pantry/index/$1";
$route['admin-add-pantry']     	 		 = "pantry/addPantry";
$route['admin-edit-pantry/(:num)']     	 = "pantry/editPantry/$1";


$route['admin-appicon']     	 			 = "appicon";
$route['admin-add-appicon']     	 		 = "appicon/addAppicon";
$route['admin-edit-appicon/(:num)']     	 = "appicon/editAppicon/$1";




$route['content-management']     	 		= "pages/index";
$route['content-management/(:any)']     	= "pages/index/$1";




 


/*********************  Api Routing ****************************************/



$route['api/signup'] 						= "api/SignupLoginApi/signup";
$route['api/login'] 						= "api/SignupLoginApi/login";
$route['api/profile'] 						= "api/UserApi/profile";
$route['api/update-selfie'] 				= "api/UserApi/updateSelfie";

$route['api/languages'] 					= "api/UserApi/getlanguages";
$route['api/user-setting'] 					= "api/UserApi/getUserSetting";
$route['api/update-setting'] 				= "api/UserApi/upadateUserSetting";
$route['api/update-money'] 					= "api/UserApi/updateusermoney";
$route['api/user-money'] 					= "api/UserApi/getUserTotalMoney";
$route['api/reset-money'] 					= "api/UserApi/resetMoney";

						/*for set-up page*/
$route['api/selfie-type'] 					= "api/UserApi/updateSelfieType";

$route['api/goshopping-audiotype'] 			= "api/UserApi/updateGoShoppingAudioType";
$route['api/shopping-audiotype'] 			= "api/UserApi/updateShoppingAudioType";
$route['api/shopping-type'] 				= "api/UserApi/updateShoppingType";
$route['api/mymoney-audiotype'] 			= "api/UserApi/updateMymoneyAudioType";

$route['api/moneymanagement-audiotype'] 	= "api/UserApi/updateMoneyManagementAudioType";
$route['api/lowmoneywarning-type'] 			= "api/UserApi/updateLowMoneyWarningType";
$route['api/language-type'] 				= "api/UserApi/updateLanguageType";
$route['api/update-name'] 					= "api/UserApi/updateName";
$route['api/update-lowmoney'] 				= "api/UserApi/updateLowMoney";


							/*end*/
$route['api/pantry'] 						= "api/PantryApi/getpantry";



