<?php

// INIT

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    echo '';
}

require './cfg/general.inc.php';
require './includes/core/functions.php';

init_classes();
init_controllers_call();

DB::connect();

// VARS

$location = isset($_POST['location']) ? flt_input($_POST['location']) : null;
$data = isset($_POST['data']) ? flt_input($_POST['data']) : null;

$dpt = $location['dpt'] ?? null;
$act = $location['act'] ?? null;

// SESSION

Session::init(1);
Route::route_call($dpt, $act, $data);
