# Installation

Requirements: (PHP 5 >= 5.2.0)



After downloading the project you have to include the Zend Framework 1.11.11. Either install it globally or have a look at library/README.txt for instructions to include it locally.

Setup your database parameters in **application/configs/application.ini**

To initialize the database (**all data will be dropped**) run scripts/load.mysql.php e.g.:

`cd /path/to/folder/scripts; php load.mysql.php --env development --withdata`

Ensure: /data/exams needs 775 permissions

## Unpacking
Requirements: (PECL zip >= 1.5.0, PECL rar >= 2.0.0)

Ensure that *zlibc* is installed! (PECL zip depends on zlibc)

For using the unrar function install *PECL rar*:
`pecl -v install rar`
requires: *php5-dev dh-make-php build-essential*

##Development
There some file you should remove from the index:<br>
`git update-index --assume-unchanged logs/info.log`<br>
`git update-index --assume-unchanged logs/warn.log`<br>
`git update-index --assume-unchanged application/configs/application.ini`

# License
This project is licensed under [CC BY-SA 3.0](http://creativecommons.org/licenses/by-sa/3.0/)
