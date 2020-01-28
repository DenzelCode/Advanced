; <?php die('Forbidden'); ?>  -*- conf -*-
; Do not remove the above line, it is all that prevents this file from
; being downloaded.
;
; config.php file for PHP Shell
; Copyright (C) 2005-2020 the Phpshell-team
; Licensed under the GNU GPL.  See the file COPYING for details.

; This ini-file has three parts:
;
; * [users] where you add usernames and passwords to give users access
;   to PHP Shell.
;
; * [aliases] where you can configure shell aliases.
;
; * [settings] where general settings are placed.


[users]

; The default configuration has no users defined, you have to add your
; own (choose good passwords!).
;
; Use pwhash.php to create entries
; username = "hashed password"
; which you can add here.
; PHP Shell uses PHPs password_hash() / password_verify() functions.
; Unencrypted passwords are no longer supported.

[aliases]

; Alias expansion.  Change the two examples as needed and add your own
; favorites --- feel free to suggest more defaults!  The command line
; you enter will only be expanded on the very first token and only
; once, so having 'ls' expand into 'ls -CvhF' does not cause an
; infinite recursion.

ls = "ls -CvhF"
ll = "ls -lvhF"



[settings]

; General settings for PHP Shell.

; Home directory.  PHP Shell will change to this directory upon
; startup and whenever a bare 'cd' command is given.  This can be an
; absolute path or a path relative to the PHP Shell installation
; directory.

home-directory = "."

; Safe Mode warning.  PHP Shell will normally display a big, fat
; warning if it detects that PHP is running in Safe Mode.  If you find
; that PHP Shell works anyway, then set this to false to get rid of
; the warning.

safe-mode-warning = true

; Prompt string $PS1 ($PS2, $PS3 and $PS4 can not occur when using phpshell, 
; since commands are non-interacive!)

PS1 = "$ "

; Enable File upload. Do you want to use the file upload function?

file-upload = false

; Bind session to the user's IP address. Set to 'true' (default) for the most 
; security. If you want to continue the same logged in session from a different
; IP address, (for example because you want to connect your laptop to different
; Wifi networks without logging in again) set this to 'false'. 

bind-user-IP = true


; The login remains valid for this many minutes before re-login is required. 
; Note that the timeout happens regardless of whether there is any user 
; activity. After the timeout expires, the user is prompted again for his/her
; password, and can then continue the session. 
; 
; Note that most PHP configurations also remove sessions after a period of 
; inactivity. 
; 
; Set to 0 to disable authentication timeouts. 

timeout = 180


; If 'enable-rate-limiting' is set to 'true', PHP Shell will limit the number 
; of login attempts a remote computer can attempt. Enabling this is an 
; important security measure against someone attempting to brute-force the 
; users password. If enabled, PHP Shell will require a user to wait a number of
; seconds between each failed login attempt, where the amount of wait time 
; rises exponentially if multiple failed login attempts are made. 
; 'rate-limit-file' should be set to a filename where PHP Shell can save 
; failed login attempts. If it is unset PHP Shell creates a file in the 
; temporary directory, named something like 
; /tmp/floodcontrol_f0a60f340381c160141baa6d1f058f63 . 

enable-rate-limiting = true
rate-limit-file = 

