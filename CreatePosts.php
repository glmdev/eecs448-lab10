<?php

require_once './configuration.php';

$request = common\Request::capture();
$page = new common\Page;
$users = new app\UserRepository;
$posts = new app\PostRepository;

$page->title('Exercise 3 - EECS 448 Lab 10')
    ->header('Create a New Post');

if ( !$request->input('username') ) {
    $page->fail_to('You must specify the username.', system_url('CreatePosts.html'));
}

if ( !$request->input('content') ) {
    $page->fail_to('You must specify post content.', system_url('CreatePosts.html'));
}

$user = $users->find_one([ 'username' => $request->input('username') ]);
if ( !$user ) {
    $page->fail_to('Sorry, a user with that username does not exist.', system_url('CreatePosts.html'));
}

$post = $posts->save([
    'user_id' => $user['user_id'],
    'content' => $request->input('content'),
]);

$page->div(function() use($page) {
    $page->writes('Post created successfully.');
});

$page->write();
