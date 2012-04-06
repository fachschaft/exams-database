<?
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
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,$this->_username);
		}
		
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->_username);
	}
	
}
?>