<?php

require './configuration.php';

$request = common\Request::capture();
$page = new common\Page;
$users = new app\UserRepository;
$posts = new app\PostRepository;

$page->title('EECS 448 Lab 10 - Exercise 6')
    ->header('View Posts by User');

if ( !$request->input('user_id') ) {
    $page->fail_to('Please select a user.', system_url('ViewUserPosts.html'));
}

$user = $users->find_by_id($request->input('user_id'));

if ( !$user ) {
    $page->fail_to('Invalid user.', system_url('ViewUserPosts.html'));
}

$user_posts = $posts->find([
    'user_id' => $user['user_id'],
]);

$post_display = array_merge([
    ['Post ID', 'Post Content']
], array_map(function($post) {
    return [$post['post_id'], $post['content']];
}, $user_posts));


$page->div(function() use($page, $user) {
    $page->writes('<b>Posts by ' . $user['username'] . ':</b>');
});

$page->table($post_display);

$page->div(function() use($page) {
    $page->writes('<a href="' . system_url('ViewUserPosts.html') . '">Select a different user</a>');
});

$page->write();
