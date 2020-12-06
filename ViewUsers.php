<?php

require './configuration.php';

$page = new common\Page;
$users = new app\UserRepository;

$page->title('EECS 448 Lab 10 - Exercise 5')
    ->header('View Registered Users');

$registered_users = $users->find();

$table_display = array_merge([
    // Add the table headers
    ['User ID', 'Username'],
], array_map(function($user) {
    // Map from associative array to keyed
    return array_values($user);
}, $registered_users));

$page->table($table_display);

$page->write();
