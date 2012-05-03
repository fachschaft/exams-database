<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Model_AuthManager {
	
	public function pushParameters($params) {
		if (! isset ( $params ['rcontroller'] ) || ! isset ( $params ['raction'] )) {
			$params ['rcontroller'] = $params ['controller'];
			$params ['raction'] = $params ['action'];
		}
		unset ( $params ['controller'] );
		unset ( $params ['action'] );
		
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
		
		return $params;
	}
	
	public function grantPermission($data){
		$adapter = $this->getAuthAdapter ($data);
		$auth = Zend_Auth::getInstance ();
		$result = $auth->authenticate ( $adapter );
		if ($result->isValid())
			return true;
		else
			return false;
	}
	
	private function getAuthAdapter(array $params) {
		// Set up the authentication adapter
		// $config = Zend_Registry::get ( 'authenticate' );
		// return new Zend_Auth_Adapter_Digest($config['filename'],
		// $config['realm'], $params['username'], $params['password']);
		// 
		if (isset ($params['username']) && isset( $params['password']))
			return new Custom_Auth_Adapter_Simple ( $params ['username'], $params ['password'] );
		
		elseif (isset($params['ip']))
			return new Custom_Auth_Adapter_InternetProtocol($params['ip']);
		
		else 
			throw new Exception('Could not get Auth adapter');
	}

}

