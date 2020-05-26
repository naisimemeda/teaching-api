<?php

use Illuminate\Http\Request;

// 教师注册申请
Route::post('teacher', 'AuthController@store')->name('auth.teacher.store');
// 申请令牌
Route::post('login', 'AuthController@login')->name('auth.login');
//接受邀请
Route::post('accept/invite', 'Admin\TeacherController@acceptInvitation');

Route::any('valid/notification', 'AuthController@validNotification')->name('auth.validNotification');

Route::post('line', 'AuthController@line')->name('line');

Route::post('upload/file', 'ImageController@upload')->middleware('auth:api');

Route::get('line/account', 'AuthController@lineAccountList')->name('line.login');

Route::post('line/auth', 'AuthController@lineAuth')->name('line.auth');

// 需要老师认证的接口
Route::group(['prefix' => '', 'middleware' => ['auth:api'], 'namespace' => 'Admin'], function () {
    Route::get('teachers/me', 'TeacherController@me');
    //学校列表
    Route::get('schools', 'SchoolController@index')->name('school.index');
    //申请学校
    Route::post('schools', 'SchoolController@store')->name('school.store');
    Route::get('schools/option', 'SchoolController@option')->name('school.option');
    // 学生列表
    Route::get('schools/{school}/students', 'StudentController@schoolIndex')->name('student.index');
    // 教师列表
    Route::get('schools/{school}/teachers', 'TeacherController@schoolIndex')->name('student.index');
    // 添加学生
    Route::post('students', 'StudentController@store')->name('student.store');
    // 邀请教师
    Route::post('schools/invite/teacher', 'TeacherController@inviteTeacher')->name('school.invite');

    Route::get('teacher/fans', 'FollowerController@index')->name('teacher.fans.index');

    Route::get('chat/logs', 'ChatController@chatStudent')->name('chat.student');

    Route::get('chat/messages', 'ChatController@chatMessages')->name('chat.messages');

    Route::post('send/messages', 'ChatController@sendStudentChatMessage')->name('chat.send.message');

    Route::get('line-binding/token', 'AuthController@getLineBindToken')->name('line.binding.token');

});

// 需要学生认证的接口
Route::group(['prefix' => '', 'middleware' => ['auth:api']], function () {
    //学校列表
    Route::post('student/me', 'StudentController@me')->name('student.me');
    Route::post('followers/{teacher}/teacher', 'StudentController@follower')->name('student.follower');
    Route::delete('followers/{teacher}/teacher', 'StudentController@disFollower')->name('student.disFollower');
    Route::get('followers/teacher', 'StudentController@followerTeachers')->name('student.follower.teacher');
    Route::get('teachers', 'StudentController@teachersList')->name('student.teachers');
    Route::get('chat/student/messages', 'Admin\ChatController@getStudentMessages')->name('student.chat.messages');
    Route::post('student/send/messages', 'Admin\ChatController@sendTeacherMessage')->name('student.send.message');
});
