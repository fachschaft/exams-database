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

class Application_Model_API
{	
	protected $_dbTable;
	
	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	
	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Application_Model_DbTable_APIMasterKey');
		}
		return $this->_dbTable;
	}
	
	
	public function uncheckedExams($apiKey)
	{
		// check if api key ist valid
		if($apiKey != null && $this->validateKey($apiKey)) { 
			
		
		$examMapper = new Application_Model_ExamMapper();
		return $examMapper->countUnchecked();
		} else {
			exit();
		}
	}
	
	private function validateKey($apiKey)
	{
		$result = $this->getDbTable()->getAdapter()->query("SELECT count(*) as count FROM  `api_master_key` WHERE  `key` LIKE  '".$apiKey."'");
		foreach ($result as $res) {
			if($res['count'] == 1)
			{
				return true;
			}
		}
		return false;
	}

}

