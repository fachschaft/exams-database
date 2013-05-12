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

class Application_Model_UserPrivilegeMapping
{
	
	protected $_authadapter;
	protected $_identity;
	protected $_role;
	
	/**
	 * @return the $_authadapter
	 */
	public function getAuthadapter() {
		return $this->_authadapter;
	}

	/**
	 * @return the $_identity
	 */
	public function getIdentity() {
		return $this->_identity;
	}

	/**
	 * @return the $_role
	 */
	public function getRole() {
		return $this->_role;
	}

	/**
	 * @param field_type $_authadapter
	 */
	public function setAuthadapter($_authadapter) {
		$this->_authadapter = $_authadapter;
	}

	/**
	 * @param field_type $_identity
	 */
	public function setIdentity($_identity) {
		$this->_identity = $_identity;
	}

	/**
	 * @param field_type $_role
	 */
	public function setRole($_role) {
		$this->_role = $_role;
	}

	public function __construct(array $options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid property in the following model: UserPrivilegeMapping');
		}
		$this->$method($value);
	}
	
	public function __get($name)
	{
		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid property in the following model: UserPrivilegeMapping');
		}
		return $this->$method();
	}
	
	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}


}

