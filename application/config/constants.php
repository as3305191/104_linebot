<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

//智付寶
define('PARTNER_ID', "");

//測試
define('HASH_KEY', '');
define('HASH_IV', '');

// line
define('CHANNEL_ACCESS_TOKEN', (ENVIRONMENT_SETUP == 'production' ? 'go8wVeBhujLrYpAf+5szEug5oaBffO+HYeOfZQ+bL4AUOiEjqN6TrJh6ROTGd1qa2jxh3XnqeJr8RUxDfjnSKEJQ80B4OhAS+teAfloTqMLB45rLtvSJvIZ7mhItuCpT1UCoOdbyI4fQ6MY05yIR5gdB04t89/1O/w1cDnyilFU=' : 'go8wVeBhujLrYpAf+5szEug5oaBffO+HYeOfZQ+bL4AUOiEjqN6TrJh6ROTGd1qa2jxh3XnqeJr8RUxDfjnSKEJQ80B4OhAS+teAfloTqMLB45rLtvSJvIZ7mhItuCpT1UCoOdbyI4fQ6MY05yIR5gdB04t89/1O/w1cDnyilFU='));
define('CHANNEL_ID', (ENVIRONMENT_SETUP == 'production' ? '1603348732' : '1603348732'));
define('CHANNEL_SECRET', (ENVIRONMENT_SETUP == 'production' ? '83bb1fa30ec4638aceb79012ecb8c12a' : '83bb1fa30ec4638aceb79012ecb8c12a'));

define('LOGIN_CHANNEL_ID', (ENVIRONMENT_SETUP == 'production' ? '1603348495' : '1603348495'));
define('LOGIN_CHANNEL_SECRET', (ENVIRONMENT_SETUP == 'production' ? '0c2db1d52348e24d56b83e12e24e58ec' : '0c2db1d52348e24d56b83e12e24e58ec'));
define('BASE_URL', (ENVIRONMENT_SETUP == 'production' ? 'https://fish.17lineplay.com/coc_bot' : 'https://fish.17lineplay.com/coc_bot'));
define('GAME_WEB_URL', (ENVIRONMENT_SETUP == 'production' ? 'https://fish.17lineplay.com/coc_bot/line_login' : 'https://fish.17lineplay.com/coc_bot/line_login'));
define('PUSH_URL', (ENVIRONMENT_SETUP == 'production' ? 'http://34.80.8.20:8988/check_push' : 'http://34.80.8.20:8988/check_push'));

define('HOME_DIR', './');

define('IMG_DIR', '../../vcs_backend_debug/img/');
define('SMS_ACCOUNT', '');
define('SMS_PASSWORD', '');
define('VERSION', '1');

define('WIN_TIE', '0');
define('WIN_DEALER', '1');
define('WIN_BANKER', '1');
define('WIN_PLAYER', '2');
define('WIN_BANKER_PAIR', '3');
define('WIN_PLAYER_PAIR', '4');
define('WIN_BANKER_AND_PLAYER_PAIR', '6');

define('PLAY_SECONDS', 30);
define('OPENING_SECONDS', 1);
define('OPEN_SECONDS', 10);
define('BONUS_SECONDS', 3);

define('WANG_URL', 'http://scratch-demo-api.wangzugames.com/waa/v1/');

define('RACE_BET_SECONDS', 180);
define('RACE_RACE_SECONDS', 0);
define('RACE_OPEN_SECONDS', 35);

define('NN_WAIT', 2);
define('NN_BANKER_COMPETITION', 5);
define('NN_PLAYER_TIMES', 5);
define('NN_OPENING', 12);
define('NN_BONUS', 5);
define('NN_NEXT', 2);

define('BJ_WAIT', 10);
define('BJ_PLAYING', 5);
define('BJ_WAIT_PLAYERS', 5);
define('BJ_OPENING', 5);
define('BJ_BONUS', 10);
define('BJ_NEXT', 3);
