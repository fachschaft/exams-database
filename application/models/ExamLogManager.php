<?php
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Model_ExamLogManager
{
	public static $placeholderUser = "%user%";
	public static $placeholderIp = "%ip%";
	
	
	public static function addLogMessage($examId, $message) {
			
		$message = Application_Model_ExamLogManager::replacePlaceholder($message);
	
		$logDb = new Application_Model_DbTable_ExamLog();
		$logDb->getAdapter()->query("INSERT INTO  `exam_log` (`exam_idexam` ,`message`)
				VALUES ('".$examId."',  '".$message."')");
	}
	
	
	private static function replacePlaceholder($message)
	{
		$message = preg_replace("/".Application_Model_ExamLogManager::$placeholderUser."/", Application_Model_AuthManager::getIdentity(), $message);
		
		$message = preg_replace("/".Application_Model_ExamLogManager::$placeholderIp."/", $_SERVER['REMOTE_ADDR'] , $message);
		
		return $message;
	}


}

