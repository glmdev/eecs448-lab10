<?php

namespace app;

use common\database\Repository;

class PostRepository extends Repository {
    protected $table = 'posts';
    protected $primary_key = 'post_id';
    protected $fields = ['post_id', 'user_id', 'content'];
}
