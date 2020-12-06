<?php

namespace app;

use common\database\Repository;

class UserRepository extends Repository {
    protected $table = 'users';
    protected $primary_key = 'user_id';
    protected $fields = ['user_id', 'username'];
}
