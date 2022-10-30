# CodeIgniter Task Scheduler


This makes scheduling cronjobs in your application simple, flexible, and powerful. Instead of setting up 
multiple cronjobs on each server your application runs on, you only need to setup a single cronjob to 
point to the script, and then all of your tasks are scheduled in your code. Besides that, it provides 
CLI tools to help you manage the tasks that should be ran, a Debug Toolbar collector, and more. 

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Installation via composer

Use the package with composer install
`composer require daycry/cronjob`

## Manual installation

Download this repo and then enable it by editing **app/Config/Autoload.php** and adding the **Daycry\CronJob**
namespace to the **$psr4** array. For example, if you copied it into **app/ThirdParty**:

```php
$psr4 = [
    'Config'      => APPPATH . 'Config',
    APP_NAMESPACE => APPPATH,
    'Daycry\CronJob' => APPPATH .'ThirdParty/cronjob/src',
];
```

## Configuration

Run command:

	> php spark cronjob:publish
This command will copy a config file to your app namespace.
Then you can adjust it to your needs. By default file will be present in `app/Config/CronJob.php`.

    > php spark migrate -all
This command create rest server tables in your database.

    > php spark cronjob:enable
This command use to enable the cronjob service.

    > php spark cronjob:list
This command use display the job list of actions.

## Fix and issue in Daycry\CronJob\Job
change `protected string $name` to `protected ?string $name= NULL;` to provide an issue in php7.4

## Starting the Scheduler

You only need to add a single line to your cronjob: 

    > * * * * * cd /path-to-your-project && php spark cronjob:run >> /dev/null 2>&1
    
This will call your script every minute. When `cronjob:run` is called, Tasks will determine the
correct tasks that should be run and execute them.

If you want simulate run a task in a specific time, you can pass in a param.

    > * * * * * cd /path-to-your-project && php spark cronjob:run -testTime "2021-01-01 09:45:00" >> /dev/null 2>&1
## Defining Schedules

Tasks are configured with the `app/Config/CronJob.php` config file, inside of the `init()` method.
Lets start with a simple example: 

```
<?php namespace Daycry\CronJob\Config;
use CodeIgniter\Config\BaseConfig;
use Daycry\CronJob\Scheduler;
class CronJob extends BaseConfig
{
    /*
    |--------------------------------------------------------------------------
	| Cronjobs
	|--------------------------------------------------------------------------
    |
	| Register any tasks within this method for the application.
	| Called by the TaskRunner.
	|
	| @param Scheduler $schedule
	*/
    public function init(Scheduler $schedule)
    {
        $schedule->call(function() { 
            DemoContent::refresh();
        })->everyMonday();
    }
}
```

In this example, we use a closure to refresh demo content at 12:00 am every Monday morning. Closures are 
a simple way to handle quick functions like this. You can also execute server commands, execute custom
CLI commands you have written, call a URL, or even fire off an Event of your choosing. Details are covered 
below.

### Scheduling CLI Commands

If you have written your own [CLI Commands](https://codeigniter.com/user_guide/cli/cli_commands.html), you 
can schedule them to run using the `command()` method.

```
$schedule->command('demo:refresh --all');
```  

The only argument is a string that calls the command, complete with an options or arguments. 

### Scheduling Shell Commands

You can call out to the server and execute a command using the `shell()` method.

```
$schedule->shell('cp foo bar')->daily( '11:00 pm' );
$schedule->shell('cp foo bar')->daily( '23:00' );
``` 

Simply provide the command to call and any arguments, and it will be executed using PHP's `exec()` method. 

> NOTE: Many shared servers turn off exec access for security reasons. If you will be running
> on a shared server, double-check you can use the exec command before using this feature.
### Scheduling Events

If you want to trigger an [Event](https://codeigniter.com/user_guide/extending/events.html) you can 
use the `event()` method to do that for you, passing in the name of the event to trigger.

```
$schedule->event('Foo')->hourly();
```

### Scheduling URL Calls

If you need to ping a URL on a regular basis, you can use the `url()` method to perform a simple
GET request using cURL to the URL you pass in. If you need more dynamism than can be provided in 
a simple URL string, you can use a closure or command instead.

```
$schedule->url('https://my-status-cloud.com?site=foo.com')->everyFiveMinutes();
```

### Frequency Options

There are a number of ways available to specify how often the task is called.


| Method                        | Description                                                           |
|:------------------------------|:----------------------------------------------------------------------|
| ->cron('* * * * *')           | Run on a custom cron schedule.                                        |
| ->daily('4:00 am')            | Runs daily at 12:00am, unless a time string is passed in.             |    
| ->hourly() / ->hourly(15)     | Runs at the top of every hour or at specified minute.                 |
| ->everyFiveMinutes()          | Runs every 5 minutes (12:00, 12:05, 12:10, etc)                       |
| ->everyFifteenMinutes()       | Runs every 15 minutes (12:00, 12:15, etc)                             |
| ->everyThirtyMinutes()        | Runs every 30 minutes (12:00, 12:30, etc)                             |
| ->sundays('3:15 am')           | Runs every Sunday at midnight, unless time passed in.                 |
| ->mondays('3:15 am')           | Runs every Monday at midnight, unless time passed in.                 |
| ->tuesdays('3:15 am')          | Runs every Tuesday at midnight, unless time passed in.                |
| ->wednesdays('3:15 am')        | Runs every Wednesday at midnight, unless time passed in.              |
| ->thursdays('3:15 am')         | Runs every Thursday at midnight, unless time passed in.               |
| ->fridays('3:15 am')           | Runs every Friday at midnight, unless time passed in.                 |
| ->saturdays('3:15 am')         | Runs every Saturday at midnight, unless time passed in.               |
| ->monthly('12:21 pm')          | Runs the first day of every month at 12:00am unless time passed in.   |
| ->quarterly('5:00 am')         | Runs the first day of each quarter (Jan 1, Apr 1, July 1, Oct 1)      |
| ->yearly('12:34 am')           | Runs the first day of the year.                                       |
| ->weekdays('1:23 pm')          | Runs M-F at 12:00 am unless time passed in.                           |
| ->weekends('2:34 am')          | Runs Saturday and Sunday at 12:00 am unless time passed in.           |
| ->environments('local', 'prod')   | Restricts the task to run only in the specified environments      |
| ->everyHour(3, 15)            | Runs every 3 hours at XX:15.                                          |
| ->betweenHours(6,12)          | Runs between hours 6 and 12.                                          |
| ->hours([0,10,16])            | Runs at hours 0, 10 and 16.                                           |
| ->everyMinute(20)             | Runs every 20 minutes.                                                |
| ->betweenMinutes(0,30)        | Runs between minutes 0 and 30.                                        |
| ->minutes([0,20,40])          | Runs at specific minutes 0,20 and 40.                                 |
| ->days([0,3])                 | Runs only on Sunday and Wednesday  ( 0 is Sunday , 6 is Saturday )    |
| ->daysOfMonth([1,15])         | Runs only on days 1 and 15.                                           |
| ->months([1,7])               | Runs only on January and July.                                        |



These methods can be combined to create even more nuanced timings: 

```
$schdule->command('foo)
    ->weekdays()
    ->hourly()
    ->environments('development');
```

### Naming Tasks

You can name tasks so they can be easily referenced later, such as through the CLI with the `named()` method:

```
$schedule->command('foo')->hourly()->named('foo-task');
```

# CLI Commands

Included in the package are several commands that can be ran from that CLI that provide that bit of emergency
help you might need when something is going wrong with a cron job at 1am on a Saturday. 

All commands are ran through CodeIgniter's `spark` cli tool: 

    > php spark cronjob:list
    > php spark cronjob:run
    > php spark cronjob:run -testTime "2021-01-01 09:45:00"
## Available Commands

**cronjob:list**

    > php spark cronjob:list
This will list all available tasks that have been defined in the project, along with their type and
the next time they are scheduled to run.

    +---------------+--------------+-----------------------+
    | Name          | Type         | Next Run              |
    +---------------+--------------+-----------------------+
    | emails        | command      | 2020-03-21-18:30:00   |
    +---------------+--------------+-----------------------+

**cronjob:disable**

    > php spark cronjob:disable 
Will disable the task runner manually until you enable it again. Writes a file to `{WRITEPATH}/cronJob` so 
you need to ensure that directory is writable. Default CodeIgniter permissions already have the WRITEABLE
path with write permissions. You should not need to change anything for this to work. 

**cronjob:enable**

    > php spark cronjob:enable
Will enable the task runner if it was previously disabled, allowing all tasks to resume running. 

**cronjob:run**

    > php spark cronjob:run
    
This is the primary entry point to the Tasks system. It should be called by a cron task on the server
every minute in order to be able to effectively run all of the scheduled tasks. You typically will not
run this manually.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
- xml (enabled by default - don't turn it off)
