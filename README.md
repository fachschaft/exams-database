# Installation

After downloading the project you have to include the Zend Framework 1.11.11, have a look at the [here](library/README.txt).

Setup your database parameters in **application/configs/application.ini**

For initializing the database (**all data will be dropped**) run scripts/load.mysql.php e.g.:

`cd /path/to/folder/scripts; php load.mysql.php --env development --withdata`