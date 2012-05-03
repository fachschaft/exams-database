<?
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

class Custom_Auth_Adapter_Simple implements Zend_Auth_Adapter_Interface
{
	protected $_username;
	protected $_password;
	protected $_users = array('admin'=>'123456', 'aritas'=>'123456');
	
	public function __construct($username, $password)
	{
	$this->_username=$username;
	$this->_password=$password;
	}
	
	public function authenticate()
	{
		if(!array_key_exists($this->_username,$this->_users)) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,$this->_username);
		}
		if(array_key_exists($this->_username,$this->_users) && $this->_users[$this->_username] != $this->_password) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,$this->_username);
		}
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->_username);
	}
	
}
?>