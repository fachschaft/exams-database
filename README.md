# Installation

Requires: (PHP 5 >= 5.2.0, PECL zip >= 1.5.0)

Ensure that *zlibc* is installed!

After downloading the project you have to include the Zend Framework 1.11.11. Either install it globally or have a look at library/README.txt for instructions to include it locally.

Setup your database parameters in **application/configs/application.ini**

To initialize the database (**all data will be dropped**) run scripts/load.mysql.php e.g.:

`cd /path/to/folder/scripts; php load.mysql.php --env development --withdata`

Ensure: /data/exams needs 775 permissions