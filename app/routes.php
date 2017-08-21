<?php
Route::get("/", "ListController@getIndex");
Route::controller("list", "ListController");
Route::controller("parse", "ParseController");
Route::controller("settings", "SettingsController");
