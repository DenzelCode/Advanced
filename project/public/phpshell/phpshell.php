<?php // -*- coding: utf-8 -*-

define('PHPSHELL_VERSION', '2.5');
/*

  **************************************************************
  *                     PHP Shell                              *
  **************************************************************

  PHP Shell is an interactive PHP script that will execute any command
  entered.  See the files README, INSTALL, and SECURITY or
  http://phpshell.sourceforge.net/ for further information.

  Copyright (C) 2000-2020 the Phpshell-team

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

  
*/

/* There are no user-configurable settings in this file anymore, please see
 * config.php instead. */


/* This error handler will turn all notices, warnings, and errors into fatal
 * errors, unless they have been suppressed with the @-operator. */
function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    /* The @-operator (used with chdir() below) temporarely makes
     * error_reporting() return zero, and we don't want to die in that case.
     * That happens mostly in cases where we can just ignore it. */
    if (error_reporting() != 0) {
        die('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>PHP Shell ' . PHPSHELL_VERSION . '</title>
  <meta http-equiv="Content-Script-Type" content="text/javascript">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta name="generator" content="phpshell">
  <meta name="robots" content="noindex, follow">
  <link rel="shortcut icon" type="image/x-icon" href="phpshell.ico">
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
  <h1>Fatal Error!</h1>
  <p><b>' . $errstr . '</b></p>
  <p>in <b>' . $errfile . '</b>, line <b>' . $errline . '</b>.</p>

  <form name="shell" enctype="multipart/form-data" action="" method="post"><p>
    If you want to try to reset your session: 
    <input type="submit" name="logout" value="Logout" style="display: inline;">
  </p></form>
  <hr>

  <p>Please consult the <a href="README" rel="nofollow">README</a>, <a
  href="INSTALL" rel="nofollow">INSTALL</a>, and <a href="SECURITY" rel="nofollow">SECURITY</a> files for
  instruction on how to use PHP Shell.</p>

  <hr>

  <address>
  Copyright &copy; 2000&ndash;2020, the Phpshell-team. Get the latest
  version at <a
  href="http://phpshell.sourceforge.net/">http://phpshell.sourceforge.net/</a>.
  </address>

</body>
</html>');
    }
}

/* Installing our error handler makes PHP die on even the slightest problem.
 * This is what we want in a security critical application like this. */
set_error_handler('error_handler');


/* Clear screen */
function builtin_clear($arg) {
    $_SESSION['output'] = '';
}

function stripslashes_deep($value) {
    if (is_array($value)) {
        return array_map('stripslashes_deep', $value);
    } else {
        return stripslashes($value);
    }
}


function htmlescape($value) {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE);
}


/* even though proc_open has a $cwd argument, we don't use it because php 4 
 * doesn't support it. */
function add_dir($cmd, $dir){
    return "cd ".escapeshellarg($dir)."\n".$cmd;
}

/* executes a command in the given working directory and returns output */
function exec_cwd($cmd, $directory) {   
    list($status, $stdout, $stderr) = exec_command($cmd, $directory);
    return $stdout;
}



/* 
 * Where the real magic happens
 *
 * $mergeoutputs says if the command's stdout and stderr should be separated 
 * or merged into a single string.
 * $fd9 adds an extra pipe on file descriptor 9 to the process, used for out 
 * of band communication.
 * The return value is an array containing array(status, stdout[, stderr][, fd9]) 
 * with the last two possibly being omitted. 
 */
function exec_command($cmd, $dir, $mergeoutput=false, $fd9=false) {

    $io = array();
    $pipes = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
    if ($fd9) $pipes[9] = array('pipe', 'w');
    $p = proc_open(add_dir($cmd, $dir), $pipes, $io);

    /* 
     * Read output using stream_select. Reading the pipes sequentially could
     * potentially cause a deadlock if the subshell would write a large 
     * amount of data to pipe 2 (stderr), while we are reading pipe 1. The
     * subshell would then block waiting for us to read pipe 2, and we would
     * block waiting for the subshell to write to pipe 1, resulting in a 
     * deadlock.
     */

    // set all streams to nonblocking mode, so we can read them all at once 
    // below
    foreach ($io as $pipe) {
        stream_set_blocking($pipe, 0);
    }

    $out = $err = $out9 = '';

    while (true) {
        // we need to recreate $read each time, because it gets modified in
        // stream_select. Also, we just want to select on those pipes that are
        // not closed yet. 
        $read = array();
        foreach ($io as $pipe) {
            if (!feof($pipe))
                $read[] = $pipe;
        }

        // break out if nothing more to read
        if (count($read) == 0) 
            break;

        // define these because we must pass something by reference
        $write = null;
        $except = null;

        // wait for the subshell to write to any of the pipes
        stream_select($read, $write, $except, 10000);

        // and read them. We don't bother to see which one is ready, we just 
        // try them all. That's why we put them in nonblocking mode. 
        $out .= fgets($io[1]);
        if ($mergeoutput) {
            $out .= fgets($io[2]);
        } else {
            $err .= fgets($io[2]);
        }
        if ($fd9) {
            $out9 .= fgets($io[9]);
        }
    }

    fclose($io[1]);
    fclose($io[2]);
    if ($fd9) fclose($io[9]);
    $status = proc_close($p);
    $ret = array($status, $out);
    if (!$mergeoutput) $ret[] = $err;
    if ($fd9) $ret[] = $out9;    
    return $ret;
}

function setdefault(&$var, $options) {
    foreach ($options as $opt) {
        if ($opt != '') {
            $var = $opt;
            return;
        }
    }
}

function reset_csrf_token() {
    $_SESSION['csrf_token'] = base64_encode(random_bytes(16));
}

// Re-generate a session id. Only has effect if session_start is called lateron.
function reset_session_id() {
    $newid = bin2hex(random_bytes(16));
    return session_id($newid);
}

// Generate a new session id for a running session. 
function reset_session() {
    session_destroy();
    reset_session_id();
    session_start();
}


function runcommand($cmd) {
    global $rows, $columns, $ini;

    $extra_env = 
        "export ROWS=$rows\n".
        "export COLUMNS=$columns\n".
        "export HOME=\"" . realpath($ini['settings']['home-directory']) . "\"\n";

    $aliases = '';
    foreach ($ini['aliases'] as $al => $expansion) {
        $aliases .= "alias $al=".escapeshellarg($expansion)."\n";
    }

    $command = 
        $extra_env.
        $aliases.
        $cmd." \n".   # extra space in case the command ends in \
        "pwd >&9\n";

    list($status, $out, $newcwd) = exec_command($command, $_SESSION['cwd'], true, true);

    // trim because 'pwd' adds a newline
    if (strlen($newcwd) > 0 && $newcwd{0} == '/')
        $_SESSION['cwd'] = trim($newcwd);

    $_SESSION['output'] .= htmlescape($out);
}    


function builtin_download($arg) {
    /* download specified file */

    if ($arg == '') {
        $_SESSION['output'] .= "Syntax: download filename\n(you forgot filename)\n";
        return;
    }

    if ($arg[0] != '/')  {
        $downloadfn = $_SESSION['cwd'] . '/' . $arg ;
    } else {
        $downloadfn = $arg ;
    }
  
    /* test if file exists */
    clearstatcache();
    if (!file_exists($downloadfn)) {
        $_SESSION['output'] .= "download: file not found: '$arg'\n";
        return;
    }

    if (!is_readable($downloadfn)) {
        $_SESSION['output'] .= "download: Permission denied for file '$arg'\n";
        return;
    }

    /* Passing a filename correctly in a content disposition header is nigh 
     * impossible. If the filename is unsafe, we just pass nothing and let the
     * user choose himself. 
     * The 'rules' are at http://tools.ietf.org/html/rfc6266#appendix-D
     * If problematic characters are encountered we use the filename*= form, 
     * user agents that don't support that don't get a filename hint. 
     */
    $basename = basename($arg);
    // match non-ascii, non printable, and '%', '\', '"'. 
    if (preg_match('/[\x00-\x1F\x80-\xFF\x7F%\\\\"]/', $basename)) {
        // Assume UTF-8 on the file system, since there's no way to check
        $filename_hdr = "filename*=UTF-8''".rawurlencode($basename).';';
    } else {
        $filename_hdr = 'filename="'.$basename.'";';
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; '.$filename_hdr);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
    if (filesize($downloadfn)) {
        header('Content-Length: '.filesize($downloadfn));
    }
    ob_clean();
    flush();
    readfile($downloadfn);
    exit();
}

/* This is a tiny editor which you can start calling 'editor file'*/
function builtin_editor($arg) {
    global $editorcontent, $filetoedit, $showeditor, $writeaccesswarning;

    if ($arg == '') {
        $_SESSION['output'] .= " Syntax: editor filename\n (you forgot the filename)\n";
        return;
    }

    $filetoedit = $arg;

    clearstatcache();
    if (!file_exists($_SESSION['cwd'] . '/' . $arg)) {
        // file does not exist
        $editorcontent = '';
        $showeditor = true;

        // test current directory for write access
        if (!is_writeable($_SESSION['cwd'])) {
            $writeaccesswarning = true;
        }

    } else {

        if (!is_file($_SESSION['cwd'] . '/' . $arg)) {
            $_SESSION['output'] .= "editor: file '$arg' not found or not a regular file\n";
            return;
        }

        if (!is_readable($_SESSION['cwd'] . '/' . $arg)) {
            $_SESSION['output'] .= "editor: Permission denied for file '$arg'\n";
            return;
        }

        if (!is_writeable($_SESSION['cwd'] . '/' . $arg)) {
            $writeaccesswarning = true;
        }

        // file_get_contents() would be possible instead of implode('', file(...)),
        // but that should work with php < 4.3
        $editorcontent = implode('', file($_SESSION['cwd'] . '/' . $arg));
        $showeditor = true;
    }

    return;
}

function builtin_logout($arg = null) {

    reset_session();

    /* Empty the session data, except for the 'authenticated' entry which the
     * rest of the code needs to be able to check. */
    $_SESSION = array('authenticated' => false);

    /* Reset the csrf token, as otherwise the login form won't render */
    reset_csrf_token();
}

function builtin_history($arg) {
    /* history command (without parameter) - output the command history */
    if (trim($arg) == '') {
        $i = 1;
        foreach ($_SESSION['history'] as $histline) {
            $_SESSION['output'] .= htmlescape(sprintf("%5d  %s\n", $i, $histline));
            $i++;
        }
    /* history command (with parameter "-c") - clear the command history */
    } elseif (preg_match('/^[[:blank:]]*-c[[:blank:]]*$/', $arg)) {
        $_SESSION['history'] = array() ;
    }
}


/* 
 * To be as safe as possible against brute-force password guessing attempts and
 * against DOS attacks that try to exploit the expensive password checking of 
 * blowfish, we read and parse the ratelimit file twice. First to see if we 
 * should attempt to authenticate at all or if there's still a timeout in force, 
 * second to clear or increase the current user's failed login attempts. Keeping
 * the file opened and locked during the password verification would provide an
 * attack vector to DOS attacks. When recording the result of that verification 
 * we need to parse the file again in case there have been any updates 
 * inbetween. However, the file is simple to parse so the parsing step is 
 * probably much faster than the password verification. 
 * 
 * PHP Shell assumes file locking will work. It won't work if the file is stored
 * on a FAT volume, or if php is running in multithreaded (instead of 
 * multiprocess) mode. Both are unlikely as FAT is quite outdated, and many PHP
 * extensions are not thread-safe so PHP hosting providers usually don't run PHP
 * in multithreaded mode. 
 */
class RateLimit {

    var $filename;
    var $intemp;

    function RateLimit() {
        global $ini;
        if (strlen(trim($ini['settings']['rate-limit-file']))) {
            $this->filename = $ini['settings']['rate-limit-file'];
            $this->intemp = false;
        } else {
            $tempdir = function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : '';
            if (!@is_dir($tempdir)) {
                $tempdir = (string) getenv('TMPDIR');
            }
            if (!@is_dir($tempdir)) {
                $tempdir = '/tmp';
            }
            // the md5 is not for security, just obfuscation
            $this->filename = $tempdir.'/floodcontrol_'.md5('PHP Shell '.$_SERVER['SERVER_NAME']);
            $this->intemp = true;
        }
    }

    function parse_file($str) {
        $parsed = array();
        foreach (explode("\n", $str) as $line) {
            $a = explode(' ', rtrim($line));
            if (count($a) < 3) {
                continue;
            }
            list($ip, $count, $timestamp) = $a;
            $parsed[$ip] = array('count' => $count, 'timestamp' => $timestamp);
        }
        return $parsed;
    }

    function serialize_table($table) {
        $a = array();
        foreach ($table as $ip => $row) {
            $a[] = "$ip {$row['count']} {$row['timestamp']}\n";
        }
        return implode('', $a);
    }

    function gc_table($table) {
        // remove entries older than a week
        $limit = time() - 60 * 60 * 24 * 7; 
        foreach (array_keys($table) as $ip) {
            if ($table[$ip]['timestamp'] < $limit) {
                unset($table[$ip]);
            }
        }
        return $table;
    }
            

    function readfile($fh) {
        $contents = '';
        while (!feof($fh)) {
            $contents .= fread($fh, 8192);
        }
        return $contents;
    }

    function check_linked($fh, $name) {
        clearstatcache();
        $fh_stat = fstat($fh);
        $name_stat = @stat($name);
        return !is_null($name_stat) && 
                $fh_stat['dev'] === $name_stat['dev'] &&
                $fh_stat['ino'] === $name_stat['ino'];
        }
    
    // returns an array with the current timeout and the next timeout if the 
    // user needs to try again. 
    function get_timeout() {
        if (!file_exists($this->filename)) {
            return 0;
        }
        $fh = fopen($this->filename, 'r');
        flock($fh, LOCK_SH);
        $linked = $this->check_linked($fh, $this->filename);
        $contents = $this->readfile($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
        $table = $this->parse_file($contents);
        if (!$linked) {
            return $this->get_timeout();
        }

        if (!isset($table[$_SERVER['REMOTE_ADDR']])) {
            return 0;
        } else {
            $record = $table[$_SERVER['REMOTE_ADDR']];
            // start counting only on the third failed try
            $timeout = (int) pow(2, $record['count']-2);
            $waited = time() - $record['timestamp'];
            $nexttimeout = (int) pow(2, $record['count']-1);
            return array(max(0, $timeout - $waited), $nexttimeout);
        }
    }

    // register a failed login of the current user
    function register_user() {
        $fh = fopen($this->filename, 'a+');
        if ($this->intemp) {chmod($this->filename, 0640);}
        flock($fh, LOCK_EX);
        $linked = $this->check_linked($fh, $this->filename);
        $table = $this->gc_table($this->parse_file($this->readfile($fh)));
        $ip = $_SERVER['REMOTE_ADDR'];
        $table[$ip] = array('count' => @$table[$ip]['count']+1, 'timestamp' => time());
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, $this->serialize_table($table));
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
        if (!$linked) {
            return $this->register_user();
        }
    }

    // a successful login, clear failed login attempts for user
    function clear_user() {
        if (!file_exists($this->filename)) {
            return;
        }
        $fh = fopen($this->filename, 'a+');
        if ($this->intemp) {chmod($this->filename, 0640);}
        flock($fh, LOCK_EX);
        $linked = $this->check_linked($fh, $this->filename);
        $table = $this->gc_table($this->parse_file($this->readfile($fh)));
        unset($table[$_SERVER['REMOTE_ADDR']]);
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, $this->serialize_table($table));
        fflush($fh);
        if ($linked && $this->intemp && count($table) == 0) {
            @unlink($this->filename);
        }
        flock($fh, LOCK_UN);
        fclose($fh);
        if (!$linked) {
            return $this->clear_user();
        }
    }
}

// attempt to authenticate but prevent brute forcing
function try_authenticate($username, $password) {
    global $ini, $warning;
    $nextwait = 0;
    if ($ini['settings']['enable-rate-limiting']) {
        $rl = new RateLimit();
        list ($wait, $nextwait) = $rl->get_timeout();
        if ($wait) {
            $minutes = floor($wait / 60);
            $waittxt = $minutes ? $minutes.' minutes and ' : '';
            $waittxt .= ($wait % 60).' seconds';
            $warning .= "<p class='warning'><b>Error:</b> Too many failed login attempts, 
                please wait <span id='waitcount'>$waittxt</span> more before 
                re-trying to log in.</p><script type='text/javascript'>startcountdown($wait, 'waitcount');
                </script>\n";
            return false;
        }
        $authenticated = authenticate($username, $password);
        if ($authenticated) {
            $rl->clear_user();
        } else {
            $rl->register_user();
        }
    } else {
        $authenticated = authenticate($username, $password);
    }
    if (!$authenticated && $nextwait >= 2) {
        $minutes = floor($nextwait / 60);
        $waittxt = $minutes ? $minutes.' minutes and ' : '';
        $waittxt .= ($nextwait % 60).' seconds';
        $warning .= "<p class=\"error\">Login failed, please try again in 
            <span id='waitcount'>$waittxt</span>:</p>
            <script type='text/javascript'>startcountdown($nextwait, 'waitcount')</script>\n";
    } elseif (!$authenticated) {
        $warning .= "<p class=\"error\">Login failed, please try again:</p>\n";
    }
    return $authenticated;
}

// returns true if authentication was successful, false if not
function authenticate($username, $password) {
    global $ini, $warning;

    if (!isset($ini['users'][$username])) {
        return false;
    }
    $ini_passwordhash = $ini['users'][$username];
    return password_verify($password, $ini_passwordhash);
}



/* the builtins this shell recognizes */
$builtins = array(
    'download' => 'builtin_download',
    'editor' => 'builtin_editor',
    'exit' => 'builtin_logout',
    'logout' => 'builtin_logout',
    'history' => 'builtin_history',
    'clear' => 'builtin_clear');



/** initialize everything **/

/** Load the configuration. **/
$ini = parse_ini_file('config.php', true);

if (empty($ini['settings'])) {
    $ini['settings'] = array();
}

/* Default settings --- these settings should always be set to something. */
$default_settings = array(
    'home-directory'        => '.',
    'safe-mode-warning'     => true,
    'file-upload'           => false,
    'PS1'                   => '$ ',
    'bind-user-IP'          => true, 
    'timeout'               => 180,
    'enable-rate-limiting'  => true,
    'rate-limit-file'       => '');
// Controls if we are in editor mode
$showeditor = false;
// Show warning if we're editing a file we can't write to
$writeaccesswarning = false;
// Did we try to authenticate the users password during this request?
$passwordchecked = false;
// Append any html to this string for warning/error messages
$warning = '';

/* Merge settings. */
$ini['settings'] = array_merge($default_settings, $ini['settings']);


/** initialize session **/

$newsession = !isset($_COOKIE[session_name()]);
$https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$expiredsession = false;

ini_set('session.use_only_cookies', '1');

if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
    session_set_cookie_params(0,    // cookie lifetime until browser closes
        $_SERVER['REQUEST_URI'],    // bind cookie to this specific URI
        null,                       // use default domain (www.site.com)
        $https,                     // If called over HTTPS, lock cookie to that
        true                        // httponly, available since PHP 5.2
    );
} else {
    // same as above, but without 'httponly'
    session_set_cookie_params(0, $_SERVER['REQUEST_URI'], null, $https);
}

if ($newsession) {
    reset_session_id();
}

session_start();
if (!$newsession && $_SESSION == array()) {
    $expiredsession = true;
    // Either the session expired, or the client invented its own session cookie. 
    // Don't allow the client to choose their own session ID. 
    reset_session();
}


if (!isset($_SESSION['csrf_token'])) {
    reset_csrf_token();
}

/* done initialising session */

/** get POST variables **/

if (get_magic_quotes_gpc()) {
    $_POST = stripslashes_deep($_POST);
}

/* Initialize some variables we need */
setdefault($_SESSION['env']['rows'], array(@$_POST['rows'], @$_SESSION['env']['rows'], 24));
setdefault($_SESSION['env']['columns'], array(@$_POST['columns'], @$_SESSION['env']['columns'], 80));

if (!preg_match('/^[[:digit:]]+$/', $_SESSION['env']['rows'])) { 
    $_SESSION['env']['rows']=24 ; 
} 
if (!preg_match('/^[[:digit:]]+$/', $_SESSION['env']['columns'])) {
    $_SESSION['env']['columns']=80 ;
}
$rows = $_SESSION['env']['rows'];
$columns = $_SESSION['env']['columns'];


/* initialisation completed, start processing */


header("Content-Type: text/html; charset=utf-8");


/* Delete the session data if the user requested a logout. 
 * Logging out is allowed without the CSRF token or other security checks, so 
 * someone can still logout if there's an error in the rest of the code. 
 * This also means that an attacker using CSRF can force someone to logout, but
 * that is not an important security problem. */
if (isset($_POST['logout'])) {
    builtin_logout('');
// Check CSRF token
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['csrf_token'] != $_SESSION['csrf_token']) {
    // Whoops, a possible cross-site request forgery attack!
    // But possibly it's just that the session expired. 
    if ($expiredsession) {
        $warning .= "<p class='error'>Session timed out</p>\n";
    } else {
        $warning .= "<p class='error'>Error: CSRF token failure</p>\n";
    }
    // Clear any POST commands, treat this request like a GET. 
    $_POST = array();
}
// Enforce session security settings
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['authenticated'] = false;
}
if (!$newsession && $_SESSION['authenticated']) {
    if ($ini['settings']['bind-user-IP'] && $_SESSION['user-IP'] != $_SERVER['REMOTE_ADDR']) {
        $_SESSION['authenticated'] = false;
    }
    if ($ini['settings']['timeout'] != 0 && 
            (time() - $_SESSION['login-timestamp']) / 60 > $ini['settings']['timeout']) {
        $_SESSION['authenticated'] = false;
    }
}

/* set some variables we need a lot */
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$command  = isset($_POST['command'])  ? $_POST['command']  : '';

/* Attempt authentication. */
if (isset($_SESSION['nonce']) && isset($_POST['nonce']) && 
        $_POST['nonce'] == $_SESSION['nonce'] && isset($_POST['login'])) {
    unset($_SESSION['nonce']);
    $passwordchecked = true; 

    $_SESSION['authenticated'] = try_authenticate($username, $password);
    if ($passwordchecked && $_SESSION['authenticated']) {
        // For security purposes, reset the session ID if we just logged in. 
        // Preserve session parameters, re-login may be caused by e.g. a timeout. 
        $session = $_SESSION;
        reset_session();
        $_SESSION = $session;
        unset($session);
        reset_csrf_token();
        $_SESSION['login-timestamp'] = time();
        $_SESSION['user-IP'] = $_SERVER['REMOTE_ADDR'];
    }
}

/* process user commands */
if ($_SESSION['authenticated']) {  
    /* Clear screen if submitted */
    if (isset($_POST['clear'])) {
        builtin_clear('');
    }

    /* Initialize the session variables. */
    if (empty($_SESSION['cwd'])) {
        $_SESSION['cwd'] = realpath($ini['settings']['home-directory']);
        $_SESSION['history'] = array();
        $_SESSION['output'] = '';
    }

    /* Clicked on one of the subdirectory links - ignore the command */
    if (isset($_POST['levelup'])) {
        $levelup = $_POST['levelup'] ;
        while ($levelup > 0) {
            $command = '' ; /* ignore the command */
            $_SESSION['cwd'] = dirname($_SESSION['cwd']);
            $levelup -- ;
        }
    }
    /* Selected a new subdirectory as working directory - ignore the command */
    if (isset($_POST['changedirectory'])) {
        $changedir= $_POST['changedirectory'];
        if (strlen($changedir) > 0) {
            if (@chdir($_SESSION['cwd'] . '/' . $changedir)) {
                $command = '' ; /* ignore the command */
                $_SESSION['cwd'] = realpath($_SESSION['cwd'] . '/' . $changedir);
            }
        }
    }

    /* handle file uploads */
    $uploaderror = 0;
    if (isset($_FILES['uploadfile']['tmp_name'])) {
        if (is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {
            if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $_SESSION['cwd'] . '/' . $_FILES['uploadfile']['name'])) { 
                $warning .= "<p class='warning'>Error: cannot move {$_FILES['uploadfile']['name']}</p>\n" ;
            }
        } elseif (isset($_FILES['uploadfile']['error']) && $_FILES['uploadfile']['error'] != 0) {
            $uploaderror = $_FILES['uploadfile']['error'];
        }
    }

    /* Save content from 'editor' */
    if (isset($_POST['savefile']) && isset($_POST["filetoedit"]) && $_POST["filetoedit"] != "") {
        $io = array();
        $p = proc_open(add_dir('cat >'.escapeshellarg($_POST['filetoedit']), $_SESSION['cwd']), 
                       array(0 => array('pipe', 'r'), 2 => array('pipe', 'w')), $io);

        /*
         * I'm not entirely sure this approach will not deadlock, but I think 
         * it is ok. If the subshell fails it will exit and our write 
         * fails. There is one assumption though: the subshell will not block
         * on writing to it's stderr while we are not reading it. As long as 
         * the error message is small enough to fit in the kernel buffer, as 
         * is expected with sh/cat redirect errors, this is no problem, but if
         * that assumption does not hold we will have to do some uglier tricks
         * using stream_select and friends. 
         * 
         * IMPORTANT ASSUMPTION: THE ERROR MESSAGE IS SMALL ENOUGH TO FIT IN 
         * THE KERNELS BUFFER OF THE SUBSHELLS STDOUT.
         */

        /* The docs are not entirely clear whether fwrite can write only part
         * of the string to a pipe, but testing shows that php internally 
         * splits up large writes into smaller ones, so normally everything 
         * gets written. */
        $content = str_replace("\r\n", "\n", $_POST["filecontent"]);
        $status = fwrite($io[0], $content);
        /* We can't really rely on the number of bytes written if 
        $status<strlen($_POST['filecontent']), because the pipe has a kernel 
        buffer of a few kilobytes. So we don't show the number of actually 
        written bytes in the error message, just that something went wrong. */
        if ($status === false or $status < strlen($content)) {
            $_SESSION['output'] .= "editor: Error saving editor content to ".htmlescape($_POST['filetoedit'])."\n";
        }
        // close immediately to let the shell know we are done. 
        fclose($io[0]);
        // also read any error messages
        $errmsg = '';
        while (!feof($io[2])) {
            $errmsg .= fread($io[2], 8192);
        }
        if (trim($errmsg) != '') {
            $_SESSION['output'] .= htmlescape('editor: '.$errmsg);
        }
        fclose($io[2]);
        $status = proc_close($p);
        if ($status != 0) {
            $_SESSION['output'] .= "editor: Error: subprocess exited with status $status.\n";
        }
    }

    /* execute the command */
    if (trim($command) != '') {
        /* Save the command for later use in the JavaScript.  If the command is
         * already in the history, then the old entry is removed before the
         * new entry is put into the list at the front. */
        if (($i = array_search($command, $_SESSION['history'])) !== false) {
            unset($_SESSION['history'][$i]);
        }
        
        array_unshift($_SESSION['history'], $command);
  
        /* Now append the command to the output. */
        $_SESSION['output'] .= htmlescape($ini['settings']['PS1'] . $command) . "\n";

        // append a space to $command to guarantee the last capture group 
        // matches. It's removed afterward. 
        preg_match('/^[[:blank:]]*([^[:blank:]]+)([[:blank:]].*)$/', $command.' ', $regs);
        $cmd_name = $regs[1];
        $arg = trim($regs[2]);
        if (strlen($arg) > 1 && $arg{0} === substr($arg, -1) && ($arg{0} == '"' || $arg{0} == "'")) {
            $arg = substr($arg, 1, -1);
        }

        if (array_key_exists($cmd_name, $builtins)) {
            $builtins[$cmd_name]($arg);
        } else {
            /* The command is not an internal command, so we execute it and 
             * save the output. We use the full input, not the one parsed with
             * the regex above, and let the shell parse it. */
            runcommand($command);
        }
    }

    /* Build the command history for use in the JavaScript */
    if (empty($_SESSION['history'])) {
        $js_command_hist = '""';
    } else {
        $escaped = array_map('addslashes', $_SESSION['history']);
        $js_command_hist = '"", "' . implode('", "', $escaped) . '"';
    }
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>PHP Shell <?php echo PHPSHELL_VERSION ?></title>
  <meta http-equiv="Content-Script-Type" content="text/javascript">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta name="generator" content="phpshell">
  <meta name="robots" content="noindex, follow">
  <link rel="shortcut icon" type="image/x-icon" href="phpshell.ico">
  <link rel="stylesheet" href="style.css" type="text/css">

  <script type="text/javascript">
  <?php if ($_SESSION['authenticated'] && ! $showeditor) { ?>

  var current_line = 0;
  var command_hist = new Array(<?php echo $js_command_hist ?>);
  var last = 0;

  function key(e) {
    if (!e) var e = window.event;

    if (e.keyCode == 38 && current_line < command_hist.length-1) {
      command_hist[current_line] = document.shell.command.value;
      current_line++;
      document.shell.command.value = command_hist[current_line];
    }

    if (e.keyCode == 40 && current_line > 0) {
      command_hist[current_line] = document.shell.command.value;
      current_line--;
      document.shell.command.value = command_hist[current_line];
    }

  }

  function init() {
    document.shell.setAttribute("autocomplete", "off");
    document.getElementById('output').scrollTop = document.getElementById('output').scrollHeight;
    document.shell.command.focus();
  }

  <?php } elseif ($_SESSION['authenticated'] && $showeditor) { ?>

  function init() {
    document.shell.filecontent.focus();
  }

  <?php } else { /* if not authenticated */ ?>

  function init() {
    document.shell.username.focus();
  }

  <?php } ?>
    function levelup(d) {
        document.shell.levelup.value=d ; 
        document.shell.submit() ;
    }
    function changesubdir(d) {
        document.shell.changedirectory.value=document.shell.dirselected.value ; 
        document.shell.submit() ;
    }

  function startcountdown(seconds, target) {
    var targetnode = document.getElementById(target);
    var timerId = setInterval(function(){
      var minutes = Math.floor(seconds / 60);
      var text = (seconds % 60)+' seconds';
      if (minutes > 0) {
        text = minutes+' minutes and '+text;
      }
      targetnode.innerHTML = text;
      if(seconds === 0){
        clearInterval(timerId);
      }
      seconds--;
    }, 1000 );
  }

  </script>
</head>

<body onload="init()">

<h1>PHP Shell <?php echo PHPSHELL_VERSION ?></h1>

<form name="shell" enctype="multipart/form-data" action="" method="post">
<div><input name="csrf_token" type="hidden" value="<?php echo $_SESSION['csrf_token'];?>">
<input name="levelup" id="levelup" type="hidden">
<input name="changedirectory" id="changedirectory" type="hidden"></div>

<?php
if (!$_SESSION['authenticated']) {
    /* Generate a new nonce every time we present the login page.  This binds
     * each login to a unique hit on the server and prevents the simple replay
     * attack where one uses the back button in the browser to replay the POST
     * data from a login. */
    $_SESSION['nonce'] = base64_encode(random_bytes(16));

if ($ini['settings']['safe-mode-warning'] && ini_get('safe_mode')) { ?>

<div class="warning">
<b>Warning:</b> <a href="https://secure.php.net/features.safe-mode">Safe Mode</a> is enabled. PHP Shell will probably not work correctly. 
See the <a href="SECURITY">SECURITY</a> file for some background information about Safe Mode and its effects on PHP Shell.
</div>

<?php } /* Safe mode. */ ?>

<fieldset>
  <legend>Authentication</legend>

  <?php
    if (!$https) {
        echo "<p class='warning'><b>Security warning:</b> 
            You are using an unencrypted connection, this is not recommended. 
            Everything (your username, password and all your commands and results) 
            will be sent unencrypted in cleartext across the internet. Try using 
            <a href='https://".htmlescape($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."'>PHP Shell over HTTPS</a>, 
            or if that does not work, try contacting your system administrator or 
            hosting provider on how to set up HTTPS support.</p>\n";
    }
    echo $warning;
    if (empty($ini['users'])) {
        echo "<p class='warning'><b>Warning:</b>
            No user accounts are defined. You must <a href=\"pwhash.php\">configure
            some user accounts</a> before using PHP shell.</p>\n";
    } else {
        if (!$passwordchecked) {
            echo "  <p>Please login:</p>\n";
        }

        ?>
        <label for="username">Username:</label>
        <input name="username" id="username" type="text" value="<?php echo $username ?>"><br>
        <label for="password">Password:</label>
        <input name="password" id="password" type="password">
        <p><input type="submit" name="login" value="Login"></p>
        <input name="nonce" type="hidden" value="<?php echo $_SESSION['nonce']; ?>">
    <?php } ?>
</fieldset>
</form>

<?php } else { /* Authenticated. */ ?>
<fieldset>
  <legend style="background-color: transparent"><?php echo "Phpshell running on: " . $_SERVER['SERVER_NAME']; ?></legend>
<?php 
    echo $warning;
?>
<p>Current Working Directory:
<span class="pwd"><?php
    if ( $showeditor ) {
        echo htmlescape($_SESSION['cwd']) . '</span>';
    } else { /* normal mode - offer navigation via hyperlinks */
        $parts = explode('/', $_SESSION['cwd']);
     
        for ($i=1; $i<count($parts); $i=$i+1) {
            echo '<a class="pwd" title="Change to this directory. Your command will not be executed." href="javascript:levelup(' . (count($parts)-$i) . ')">/</a>' ;
            echo htmlescape($parts[$i]);
        }
        echo '</span>';
        if (is_readable($_SESSION['cwd'])) { /* is the current directory readable? */
            /* Now we make a list of the directories. */
            $dir_handle = opendir($_SESSION['cwd']);
            /* We store the output so that we can sort it later: */
            $options = array();
            /* Run through all the files and directories to find the dirs. */
            while ($dir = @readdir($dir_handle)) {
                if (($dir != '.') and ($dir != '..') and @is_dir($_SESSION['cwd'] . "/" . $dir)) {
                    $options[$dir] = "<option value=\"/$dir\">$dir</option>";
                }
            }
            closedir($dir_handle);
            if (count($options)>0) {
                ksort($options);
                echo '<br><a href="javascript:changesubdir()">Change to subdirectory</a>: <select name="dirselected">';
                echo implode("\n", $options);
                echo '</select>';
            }
        } else {
            echo "<br/><b>Notice:</b> current directory not readable.";
        }  
    }
?>
<br>

    <?php if (! $showeditor) { /* Outputs the 'terminal' without the editor */ ?>

<div class="terminal">
<pre id="output" style="height: <?php echo $rows*2 ?>ex; overflow-y: scroll;">
<?php
        $lines = substr_count($_SESSION['output'], "\n");
        $padding = str_repeat("\n", max(0, $rows - $lines));
        echo rtrim($padding . wordwrap($_SESSION['output'], $columns, "\n", true));
?>
</pre>
<p class="prompt">
<span id="ps1"><?php echo htmlescape($ini['settings']['PS1']); ?></span>
<input name="command" type="text" onkeyup="key(event)"
       size="<?php echo $columns-strlen($ini['settings']['PS1']); ?>" tabindex="1">
</p>
</div>

<?php } else { /* Output the 'editor' */ 
print "You are editing this file: <code>" . htmlescape($filetoedit) . "</code>\n"; 
if ($writeaccesswarning) { ?>

<div class="warning">
  <p><b>Warning:</b> You may not have write access to <code><?php echo htmlescape($filetoedit); ?></code></p>
</div>

<?php 
} /*write access warning*/ 
echo $warning; 
?>

<div id="terminal">
<textarea name="filecontent" id="filecontent" cols="<?php echo $columns ?>" rows="<?php echo $rows ?>">
<?php
    print(htmlescape($editorcontent));
?>
</textarea>
</div>

<?php } /* End of terminal */ ?>

<p>
<?php if (! $showeditor) { /* You can not resize the textarea while
                           * the editor is 'running', because if you would
                           * do so you would lose the changes you have
                           * already made in the textarea since last saving */
?>
  <span style="float: right">Size: <input type="text" name="rows" size="2"
  maxlength="3" value="<?php echo $rows ?>"> &times; <input type="text"
  name="columns" size="2" maxlength="3" value="<?php echo $columns
  ?>"></span><br>
<input type="submit" value="Execute command">
<input type="submit" name="clear" value="Clear screen">
<?php } else { /* for 'editor-mode' */ ?>
<input type="hidden" name="filetoedit" id="filetoedit" value="<?php print($filetoedit) ?>">
<input type="submit" name="savefile" value="Save and Exit">
<input type="reset" value="Undo all Changes">
<input type="submit" value="Exit without saving" onclick="javascript:document.getElementById('filetoedit').value='';document.getElementById('filecontent').value='';return true;">
<?php } ?>

  <input type="submit" name="logout" value="Logout">
</p>
</fieldset>
</form>

<?php if ($ini['settings']['file-upload']) { ?>
<form name="upload" enctype="multipart/form-data" action="" method="post">
<div><br><br>
<input name="csrf_token" type="hidden" value="<?php echo $_SESSION['csrf_token'];?>">
<fieldset>
  <legend>File upload</legend>
<?php
    if (isset($_FILES['uploadfile']['tmp_name']) && $uploaderror >0) {
        $uploaderrors = array(
            0=>"There is no error, the file uploaded with success",
            1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
            2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
            3=>"The uploaded file was only partially uploaded",
            4=>"No file was uploaded",
            6=>"Missing a temporary folder"
        ); 
        echo "<p class='warning'>File Upload error: $uploaderrors[$uploaderror]</p>";
    }
?>
    Select file for upload:
    <input type="file" name="uploadfile" size="40"><br>
<input type="submit" value="Upload file">
</fieldset>
</div>
</form>
    <?php } ?>

<?php } ?>

<hr>

<p>Please consult the <a href="README" rel="nofollow">README</a>, <a
href="INSTALL" rel="nofollow">INSTALL</a>, and <a href="SECURITY" rel="nofollow">SECURITY</a> files for
instruction on how to use PHP Shell.</p>
<p>If you have not created accounts for phpshell, please use 
<a href="pwhash.php">pwhash.php</a> to create secure (hashed and salted) passwords.</p>

<hr>
<address>
Copyright &copy; 2000&ndash;2020, the Phpshell-team. Get the
latest version at <a
href="http://phpshell.sourceforge.net/">http://phpshell.sourceforge.net/</a>.
</address>
</body>
</html>
