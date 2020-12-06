<?php

require './configuration.php';

$request = common\Request::capture();
$page = new common\Page;
$posts = new app\PostRepository;

$post_ids = [];
foreach ( $request->input() as $post_key => $value ) {
    if ( $value !== 'yes' ) continue;

    $post_id = explode('-', $post_key)[1];
    $post_ids[] = $post_id;

    $posts->delete(['post_id' => $post_id]);
}

$page->title('EECS 448 Lab 10 - Exercise 7')
    ->header('Posts Deleted')
    ->writes('<p>Posts with the following IDs were deleted: ' . implode(', ', $post_ids) . '</p>')
    ->writes('<a href="' . system_url('AdminHome.html') . '">Admin Home</a>');

$page->write();
