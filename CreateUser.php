<?php

require_once './configuration.php';

$request = common\Request::capture();
$page = new common\Page;
$users = new app\UserRepository;

$page->title('Exercise 2 - EECS 448 Lab 10')
    ->header('Create a New User');

if ( !$request->input('username') ) {
    $page->fail_to('You must specify a username.', system_url('CreateUser.html'));
}

$existing_user = $users->find_one([ 'username' => $request->input('username') ]);
if ( $existing_user ) {
    $page->fail_to('A user with that username already exists.', system_url('CreateUser.html'));
}

$new_user = $users->save([
    'username' => $request->input('username'),
]);

$page->div(function() use($page, $new_user) {
    $page->writes('User ' . $new_user['username'] . ' created successfully.');
});

$page->write();
