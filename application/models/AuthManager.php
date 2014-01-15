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

class Application_Model_AuthManager {
	
	static $Guest 		= array('string'=>'guest', 'order'=>0);
	static $User 		= array('string'=>'user', 'order'=>1);
	static $Admin		= array('string'=>'admin', 'order'=>2);
	static $Superadmin	= array('string'=>'superadmin', 'order'=>3);

	
	private $_acl;
	
	public function __construct()
	{
		$this->initAcl();
	}
	
	public static function mapRolteToOrder($role)
	{
		switch ($role) {
			case "guest":
				return Application_Model_AuthManager::$Guest;
			break;
			case "user":
				return Application_Model_AuthManager::$User;
				break;
			case "admin":
				return Application_Model_AuthManager::$Admin;
				break;
			case "superadmin":
				return Application_Model_AuthManager::$Superadmin;
				break;
			default:
				return Application_Model_AuthManager::$Guest;
			break;
		}
	}
	
	public function pushParameters($params) {
		if (! isset ( $params ['rcontroller'] ) || ! isset ( $params ['raction'] )) {
			$params ['rcontroller'] = $params ['controller'];
			$params ['raction'] = $params ['action'];
		}
		unset ( $params ['controller'] );
		unset ( $params ['action'] );
		
		if(isset($params['id'])) {
			$params['rid'] = $params['id'];
			unset($params['id']);
		}
		
		return $params;
	}
	
	public function popParameters($params) {
		unset ( $params ['username'] );
		unset ( $params ['password'] );
		unset ( $params ['login'] );
		
		if (! isset ( $params ['raction'] )) {
			$params ['action'] = "index";
		} 
		else {
			$params ['action'] = $params ['raction'];
			unset ( $params ['raction'] );
		}
		
		if (! isset ( $params ['rcontroller'] )) {
			$params ['controller'] = NULL;
		} 
		else {
			$params ['controller'] = $params ['rcontroller'];
			unset ( $params ['rcontroller'] );
		}
		
		if(isset($params['rid'])) {
			$params['id'] = $params['rid'];
			unset($params['rid']);
		}
		
		return $params;
	}
	
	public function grantPermission($data){
		$adapter = $this->getAuthAdapter ($data);
		$auth = Zend_Auth::getInstance ();
		$result = $auth->authenticate ( $adapter );
		if ($result->isValid()) {
			$this->storeUsedAuthAdapter($adapter);
			$this->storeRole($this->mapIdentityToRole($result->getIdentity(), get_class($adapter)));
			return true;
		}
		else {
			return false;
		}
	}
	
	private static function storeUsedAuthAdapter($adapter)
	{
		$adapt = new Zend_Session_Namespace('Used_Auth_Adapter');
		$adapt->usedAuthAdapter = get_class($adapter);
	}
	
	public static function getUsedAuthAdapter()
	{
		if(Application_Model_AuthManager::hasIdentity()) {
			$adapt = new Zend_Session_Namespace('Used_Auth_Adapter');
			return $adapt->usedAuthAdapter;
		}
		
		return null;
	}
	
	private static function storeRole($role)
	{
		$adapt = new Zend_Session_Namespace('Role');
		$adapt->role = $role;
	}
	
	public static function getRole()
	{
		if(Application_Model_AuthManager::hasIdentity()) {
			$adapt = new Zend_Session_Namespace('Role');
			return $adapt->role;
		}
	
		return null;
	}
	
	public static function getIdentity()
	{
		return Zend_Auth::getInstance ()->getIdentity();
	}
	
	public static function hasIdentity()
	{
		return Zend_Auth::getInstance ()->hasIdentity();
	}
	
	public static function clearIdentity()
	{
		return Zend_Auth::getInstance ()->clearIdentity();
	}
	
	private function getAuthAdapter(array $params) {
		// Set up the authentication adapter
		// $config = Zend_Registry::get ( 'authenticate' );
		// return new Zend_Auth_Adapter_Digest($config['filename'],
		// $config['realm'], $params['username'], $params['password']);
		
		$config_ldap = Zend_Registry::get('ldap');
		$config_ldap['server1']['username'] = str_replace("%user%", $params['username'], $config_ldap['server1']['username']);
		$config_ldap['server1']['password'] = str_replace("%pass%", $params['password'], $config_ldap['server1']['password']);
		
		//if (isset ($params['username']) && isset( $params['password']))
		//	return new Custom_Auth_Adapter_Simple ( $params ['username'], $params ['password'] );
		
		if (isset ($params['username']) && isset($params['password']))
		return new Zend_Auth_Adapter_Ldap($config_ldap, $params['username'], $params['password']);
		
		elseif (isset($params['ip']))
		return new Custom_Auth_Adapter_InternetProtocol($params['ip']);
		
		else 
			throw new Exception('Could not get Auth adapter');
	}
	
	public function initAcl()
	{
		$this->_acl = new Zend_Acl();
		$this->_acl->addRole(new Zend_Acl_Role('guest'));
		$this->_acl->addRole(new Zend_Acl_Role('user'), 'guest');
		$this->_acl->addRole(new Zend_Acl_Role('admin'), array('guest', 'user'));
		$this->_acl->addRole(new Zend_Acl_Role('superadmin'), array('guest', 'user', 'admin'));

		$this->_acl->allow('guest', null, array('search', 'quick_search', 'upload', 'report', 'view_login_form'));
		
		$this->_acl->allow('user', null, array('download'));
		
		/*
		 * view_log = read the logs
		 * approve_exam = approve, disapprove, remove_report
		 * modify_exam = delte, edit, edit files
		 */
		$this->_acl->allow('admin', null, array('view_admin_interface', 'view_log', 'approve_exam', 'modify_exam', 
												'add_degree_groups', 'modify_degree_groups', 'add_degree', 'modify_degree',
												'add_lecturer', 'modify_lecturer', 'add_course', 'modify_course'));
		
		$this->_acl->allow('superadmin', null, array('maintenance_quicksearch_new_index', 'maintenance_quickseach_rebuild_index',
													 'maintenance_quicksearch_delete_index', 'maintenance_quicksearch_exec_garbage',
													 'maintenance_quicksearch_file_count',
													 'maintenance_check_inconsistency', 'maintenance_determine_mime_types',
													 'maintenance_check_files_exist_and_readable', 'maintenance_check_files_extention',
													 'maintenance_check_damaged_files', 'maintenance_generate_missing_md5sums', 'maintenance_send_test_mail'));
		
	}
	
	public function isAllowed($res, $right)
	{
		return $this->_acl->isAllowed($this->mapIdentityToRole($this->getIdentity(), $this->getUsedAuthAdapter()), $res, $right);
	}

	
	public function mapIdentityToRole($identity, $adapter)
	{		
		if($adapter == NULL) { return 'guest'; }
		
		$privMap = new Application_Model_UserPrivilegeMappingMapper();
		
		$role = $privMap->getRole($adapter, $identity);
		
		return $role;
	}
	
	

}

