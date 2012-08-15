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
		} else {
			$identity = ' AND identity = "'.$identity.'"';
		}
		
		if($adapter == 'Custom_Auth_Adapter_InternetProtocol') {
			$res = $this->getDbTable()->fetchAll('authadapter = "'.$adapter.'" '.$identity);
			
			if(count($res) == 0) {
				// no special role set for this ip
				// select default role
				$res = $this->getDbTable()->fetchAll('authadapter = "'.$adapter.'" AND identity IS NULL');
				
				if(count($res) == 1) {
					$role = Application_Model_AuthManager::mapRolteToOrder($res[0]['role']);
					return $role['string'];
				} elseif(count($res) >= 1) {
					// more than one default role?
					$role = Application_Model_AuthManager::$Guest;
				} else {
					$role = Application_Model_AuthManager::$Guest;
				}
				return $role['string'];
			} else {
				// order
			
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
		
		$res = $this->getDbTable()->fetchAll('authadapter = "'.$adapter.'" '.$identity);
		
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

