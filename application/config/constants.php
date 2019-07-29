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
define('CHANNEL_ACCESS_TOKEN', (ENVIRONMENT_SETUP == 'production' ? 'WhT6WkHHy+ZiPkTTSMXpgIAY/h33rQGkHjEN4uUGdjg/hfRYKM/85QDEMaEDDGOj3EPX5c5uZLdUNoXvlsNVWlD1wRMo9tCqFSIxJ4PMkR08ZV5hoXer1fyJCC3OQmplbg+KXJZBZEAQoFDxaqCgjAdB04t89/1O/w1cDnyilFU=' : 'MchLOk262JP4FOd/uax9nJd+glHLd4Pc3OHuZ81Orxer+C3PHxB6v0mMuso9qnd4Ouq77PDgu1w147eMDyl7KXlhhLucaR/jI2by2kc3Zy5NKE8FZxp8O1pmzcS/YYX8JuwSMVs/0EZo7sNwtFxl8QdB04t89/1O/w1cDnyilFU='));
define('CHANNEL_ID', (ENVIRONMENT_SETUP == 'production' ? '1592889284' : '1588780850'));
define('CHANNEL_SECRET', (ENVIRONMENT_SETUP == 'production' ? '1a52f9499aa6c37e13069f6f9a7323ed' : 'e94c816b95918b71b98fae133c84a2f5'));

define('LOGIN_CHANNEL_ID', (ENVIRONMENT_SETUP == 'production' ? '1592887977' : '1588779461'));
define('LOGIN_CHANNEL_SECRET', (ENVIRONMENT_SETUP == 'production' ? '5a22619095436f6a520f62f2c733f31a' : '86f5c585b14bdd1695f9b99348eed558'));
define('BASE_URL', (ENVIRONMENT_SETUP == 'production' ? 'https://www.yunyuplay.com/fish_production' : 'https://www.yunyuplay.com/fish'));
define('GAME_WEB_URL', (ENVIRONMENT_SETUP == 'production' ? 'https://www.yunyuplay.com/fishgame/' : 'https://www.17lineplay.com/game_debug/fishTreasure/'));
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
