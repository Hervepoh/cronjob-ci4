<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Tasks extends BaseController
{
    public function index()
    {
        log_message('debug', 'Tasks run by cronjob lib');
    }
}
