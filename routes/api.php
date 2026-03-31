<?php

use App\Http\Controllers\exhibitionsController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\menuEditorController;
use App\Http\Controllers\menuGroupController;
use App\Http\Controllers\qzController;
use App\Http\Controllers\registerController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\userManagerController;
use App\Http\Controllers\visitorPrint;
use App\Http\Middleware\adminRole;
use App\Http\Middleware\menuEditorMiddleware;
use App\Http\Middleware\menuGroupMiddleware;
use App\Http\Middleware\roleMiddleware;
use App\Http\Middleware\scheduleCheck;
use App\Http\Middleware\tokenCheck;
use App\Http\Middleware\userRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$version = 'v1';
$prefixWeb = 'web';
$prefixMobile = 'mobile';

Route::prefix($version)->group(function () use ($prefixWeb, $prefixMobile) {

    Route::prefix($prefixWeb)->group(function () {
        Route::controller(loginController::class)->group(function () {
            Route::post('/auth/login', 'login');
            Route::post('/auth/login/check', 'login_check');
        });

        Route::controller(resetPasswordController::class)->group(function () {
            Route::post('/auth/reset/password', 'forgotPassword');
            Route::post('/auth/reset/password/check', 'checkToken');
            Route::post('/auth/reset/password/change', 'changePassword');
        });

        Route::middleware(tokenCheck::class)->group(function () {
            // Role Check
            Route::controller(loginController::class)->group(function () {
                Route::post('/auth/user/check', 'tokenCheck');
                Route::post('/user/navigation/get', 'getNavigation');
            });

            Route::controller(qzController::class)->group(function () {
                Route::get('/qz/cert', 'cert');
                Route::post('/qz/sign', 'sign');
            });

            Route::controller(menuGroupController::class)->group(function () {
                Route::get('/menu/group/get', 'getMenuGroup')->middleware('permission:menu_group,view');
                Route::post('/menu/group/add', 'addMenuGroup')->middleware('permission:menu_group,create');
                Route::post('/menu/group/get/id', 'getMenuGroupById')->middleware('permission:menu_group,update');
                Route::post('/menu/group/edit', 'editMenuGroup')->middleware('permission:menu_group,update');
                Route::post('/menu/group/delete', 'deleteMenuGroup')->middleware('permission:menu_group,delete');
            });

            Route::controller(menuEditorController::class)->group(function () {
                Route::get('/menu/editor/get', 'getMenuEditor')->middleware('permission:sub_menu,view');
                Route::post('/menu/editor/group/get', 'getMenuGroup')->middleware('permission:sub_menu,view');
                Route::post('/menu/editor/add', 'addMenuEditor')->middleware('permission:sub_menu,create');
                Route::post('/menu/editor/get/id', 'getMenuEditorById')->middleware('permission:sub_menu,update');
                Route::post('/menu/editor/edit', 'editMenuEditor')->middleware('permission:sub_menu,update');
                Route::post('/menu/editor/delete', 'deleteMenuEditor')->middleware('permission:sub_menu,delete');
            });

            Route::controller(roleController::class)->group(function () {
                Route::get('/role/get', 'getRole')->middleware('permission:role,view');
                Route::post('/role/menu/get', 'getRoleMenu')->middleware('permission:role,view');
                Route::post('/role/add', 'addRole')->middleware('permission:role,create');
                Route::post('/role/menu/get/id', 'getRoleMenuById')->middleware('permission:role,update');
                Route::post('/role/edit', 'editRole')->middleware('permission:role,update');
                Route::post('/role/delete', 'deleteRole')->middleware('permission:role,delete');
            });

            Route::controller(userManagerController::class)->group(function () {
                Route::get('/user/manager/get', 'getUserManager')->middleware('permission:user_manager,view');
                Route::post('/user/manager/data/get', 'getDataUserManager')->middleware('permission:user_manager,view');
                Route::post('/user/manager/add', 'addUserManager')->middleware('permission:user_manager,create');
                Route::post('/user/manager/get/id', 'getUserManagerById')->middleware('permission:user_manager,update');
                Route::post('/user/manager/edit', 'editUserManager')->middleware('permission:user_manager,update');
                Route::post('/user/manager/password/change', 'changePasswordUserManager')->middleware('permission:user_manager,update');
                Route::post('/user/manager/delete', 'deleteUserManager')->middleware('permission:user_manager,delete');
            });
        });
    });
});
