<?php

require_once 'vendor/autoload.php';

use Blab\Libs\DB;

$db = DB::getDBInstance();

$result = $db->query()
		->from('users')
		->results();

var_dump($result);
