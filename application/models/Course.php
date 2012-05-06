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

class Application_Model_Course
{
    protected $_name;
    protected $_id;
    protected $_degrees;
    protected $_connectedCourse;
    
    /**
	 * @return the $_connectedCourse
	 */
	public function getConnectedCourse() {
		return $this->_connectedCourse;
	}

	/**
	 * @param field_type $_connectedCourse
	 */
	public function setConnectedCourse(array $_connectedCourse) {
		$this->_connectedCourse = $_connectedCourse;
	}

	/**
	 * @return the $_degrees
	 */
	public function getDegrees() {
		return $this->_degrees;
	}

	/**
	 * @param field_type $_degrees
	 */
	public function setDegrees(array $_degrees) {
		$this->_degrees = $_degrees;
	}

	public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function __toString()
    {
    	return $this->_name;
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Course');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Course');
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
    
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return html_entity_decode($this->_name);
    }   
    
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }

}

