<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->get('schools', 'SchoolsController@index');
    $router->get('schools/create', 'SchoolsController@create');
    $router->post('schools', 'SchoolsController@store');
    $router->get('schools/{id}/edit', 'SchoolsController@edit');
    $router->put('schools/{id}', 'SchoolsController@update');

    $router->get('teachers', 'TeacherController@index')->name('teacher');
    $router->get('teachers/{id}/edit', 'TeacherController@edit');
    $router->get('teachers/create', 'TeacherController@create');
    $router->put('teachers/{id}', 'TeacherController@update');

    $router->get('student', 'StudentController@index')->name('student');
    $router->get('student/create', 'StudentController@create');
    $router->post('student', 'StudentController@store');
    $router->get('student/{id}/edit', 'StudentController@edit');
    $router->put('student/{id}', 'StudentController@update');
});
