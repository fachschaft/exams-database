<?php

class Application_Model_UserPrivilegeMappingMapper
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
			$this->setDbTable('Application_Model_DbTable_UserPrivilegeMapping');
		}
		return $this->_dbTable;
	}
	
	public function getRole($adapter, $identity = null)
	{
		if($identity == null) {
			$identity = "";
		}
		
		$res = $this->getDbTable()->fetchAll('authadapter = "'.$adapter.'" OR authadapter IS NULL');
		
		$role = Application_Model_AuthManager::$Guest;
		
		foreach ($res as $priv) {
			$or = Application_Model_AuthManager::mapRolteToOrder($priv['role']);
			if($role['order'] < $or['order'])
			{
				$role = $or;
			}
		return $role['string'];
	}
	}
}

