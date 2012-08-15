<?
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

class Custom_Auth_Adapter_InternetProtocol implements Zend_Auth_Adapter_Interface
{
	protected $_Ip;
	protected $_regExp = "$.*$";
	
	
	public function __construct($ip)
	{
		$config = Zend_Registry::get('authenticate');
		if(isset($config['allowedips'])) { $this->_regExp = $config['allowedips']; }
		$this->_Ip = $ip;
	}
	
	public function authenticate()
	{
		if(preg_match($this->_regExp, $this->_Ip) == 0) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,$this->_Ip);
		}
		// TODO Make this nicer by giving ips a session and checking for an admin identity in the admin controller
		// NULL so that success is returned, but no session is created so that ips can't access the admin controller
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->_Ip);
	}
	
}
?>