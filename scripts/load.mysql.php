<?
/**
 * exams-database
 * @copyright   Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since     	1.0
 * @todo        -
 */

// scripts/load.sqlite.php
 
/**
* Script for creating and loading database
*/
 
// Initialize the application path and autoloading
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
 
// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
    'withdata|w' => 'Load database with sample data',
    'env|e-s'    => 'Application environment for which to create database (defaults to development)',
    'help|h'     => 'Help -- usage message',
));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}
 
// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}
 
// Initialize values based on presence or absence of CLI options
$withData = $getopt->getOption('w');
$env      = $getopt->getOption('e');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);
 
// Initialize Zend_Application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
 
// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');
 
// let the user know whats going on (we are actually creating a
// database here)
if ('testing' != APPLICATION_ENV) {
    echo 'Writing Database in (control-c to cancel): ' . PHP_EOL;
    for ($x = 5; $x > 0; $x--) {
        echo $x . "\r"; sleep(1);
    }
}
 
// Check to see if we have a database file already
$options = $bootstrap->getOption('resources');
$dbName  = $options['db']['params']['dbname'];


$createStatement = "CREATE DATABASE IF NOT EXISTS `".$dbName."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ; COMMIT; ";
$useStatement = '';//"USE `".$dbName."`; ";


// this block executes the actual statements that were loaded from
// the schema file.
try {
    
    // create database
    $newOptions = $options['db']['params'];
    $newOptions['dbname'] = '';
    
    $db = Zend_Db::factory($options['db']['adapter'], $newOptions);
    
    $db->getConnection()->exec($createStatement);
    echo PHP_EOL;
    echo 'Database Created';
    echo PHP_EOL;
    
    
    
    $schemaSql = file_get_contents(dirname(__FILE__) . '/schema.mysql.sql');
    // use the connection directly to load sql in batches
    $schemaSql = $useStatement . $schemaSql;
    
    // add data to the querry
    if ($withData) {
        $dataSql = file_get_contents(dirname(__FILE__) . '/data.mysql.sql');
        $schemaSql .= $dataSql;
        
    }
    
    $dbAdapter->getConnection()->exec($schemaSql);
 
    echo PHP_EOL;
    echo 'Tables Created';
    echo PHP_EOL;
        
    if ($withData) {
        echo 'Data Loaded.';
        echo PHP_EOL;
    }
 
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}
 
// generally speaking, this script will be run from the command line
return true;

?>