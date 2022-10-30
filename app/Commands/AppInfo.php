<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AppInfo extends BaseCommand
{
   
    protected $group       = 'app';
    protected $name        = 'app:info';
    protected $description = 'Displays basic application information.';

    // https://codeigniter.com/user_guide/cli/cli_commands.html#creating-new-commands
    public function run(array $params)
    {
        CLI::write('PHP Version: ' . CLI::color(PHP_VERSION, 'yellow'));
        CLI::write('CI Version: ' . CLI::color(\CodeIgniter\CodeIgniter::CI_VERSION, 'yellow'));
        CLI::write('APPPATH: ' . CLI::color(APPPATH, 'yellow'));
        CLI::write('SYSTEMPATH: ' . CLI::color(SYSTEMPATH, 'yellow'));
        CLI::write('ROOTPATH: ' . CLI::color(ROOTPATH, 'yellow'));
        CLI::write('Included files: ' . CLI::color(count(get_included_files()), 'yellow'));
    }
}