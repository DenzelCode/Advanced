<?php
/*
 * pwhash.php file for PHP Shell
 * Copyright (C) 2005-2020 the Phpshell-team
 * Licensed under the GNU GPL.  See the file COPYING for details.
 *
 */

define('PHPSHELL_VERSION', '2.5');

function stripslashes_deep($value) 
{
    if (is_array($value)) {
        return array_map('stripslashes_deep', $value);
    } else {
        return stripslashes($value);
    }
}

if (get_magic_quotes_gpc()) {
    $_POST = stripslashes_deep($_POST);
}

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

/* Load the configuration. */
$ini = parse_ini_file('config.php', true);

if (empty($ini['settings'])) {
    $ini['settings'] = array();
}

?>
<?php echo '<?xml version="1.0" ?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <title>Password Hasher for PHP Shell <?php echo PHPSHELL_VERSION ?></title>
  <meta http-equiv="Content-Style-Type" content="text/css"/>
  <meta name="generator" content="phpshell"/>
  <meta name="robots" content="noindex, follow"/>
  <link rel="shortcut icon" type="image/x-icon" href="phpshell.ico"/>
  <link rel="stylesheet" href="style.css" type="text/css"/>
</head>

<body>

<h1>Password Hasher for PHP Shell <?php echo PHPSHELL_VERSION ?></h1>
<p>
This password hasher creates salted and hashed password entries for your 
PHP shell config files.
</p>

<form action="" method="post">

<fieldset>
  <legend>Username/Password</legend>
  <label for="username">Username:</label>
  <input name="username" id="username" type="text" 
         value="<?php echo htmlspecialchars($username) ?>"/>
  <br/>
  <label for="password">Password:</label>
  <input name="password" id="password" type="password" 
         value="<?php echo htmlspecialchars($password) ?>"/>
  <input type="radio" name="showhidepass" value="show" 
         onclick="document.getElementById('password').type='text';"/>Show Password /
  <input type="radio" name="showhidepass" value="hide" 
         onclick="document.getElementById('password').type='password';" 
         checked="checked"/>Hide Password

</fieldset>

<fieldset>
  <legend>Result</legend>

<?php
if ($username == '' || $password == '') {
    echo "  <p><i>Enter a username and a password and update.</i></p>\n";
} else {
    /* some reserved words are not allowed as username, because there is a 
       restriction in parse_ini_string() 
       (https://secure.php.net/manual/en/function.parse-ini-string.php) */
    if (!preg_match('/^[[:alpha:]][[:alnum:]]*$/', $username)
        || in_array($username, array('null','yes','no','true','false','on','off', 'none'))
    ) {
        echo <<<END
<p class="error">Your username cannot be one of the following reserved words: 
'null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none'.<br/>
It can contain only letters and digits and must start with a letter.<br/>
Please choose another username and try again.</p>
END;
    } else {
        echo "<p>Write the following line into <tt>config.php</tt> "; 
        echo "in the <tt>[users]</tt> section:</p>\n";

        echo "<pre>".htmlentities($username)." = &quot;" . password_hash($password, PASSWORD_DEFAULT) . "&quot;</pre>\n"; ?>
        <p>After you have done that, you can return to 
        <a href="phpshell.php">phpshell.php</a> and login.</p> 
        <?php
    }
}
?>

<p><input type="submit" value="Update"/></p>

</fieldset>

</form>


<hr/>

<address>
  Copyright &copy; the Phpshell-team, please see <a href="AUTHORS" rel="nofollow">AUTHORS</a>.
  This is PHP Shell <?php echo PHPSHELL_VERSION ?>, get the latest version at <a
  href="http://phpshell.sourceforge.net/">http://phpshell.sourceforge.net/</a>.
</address>

</body>
</html>
