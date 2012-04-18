<?php

class Application_Model_Lecturer
{
    protected $_degree;	// degree of the person
    protected $_firstName;
    protected $_name;
    protected $_id;
    protected $_degrees;
    
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
    	return $this->_name . ", " . $this->_degree . " " . $this->_firstName;
    }
 
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Lecturer');
        }
        $this->$method($value);
    } 
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Lecturer');
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
        return $this->_name;
    }

    public function setFirstName($text)
    {
        $this->_firstName = (string) $text;
        return $this;
    }
 
    public function getFirstName()
    {
        return $this->_firstName;
    } 

    public function setDegree($text)
    {
        $this->_degree = (string) $text;
        return $this;
    }
 
    public function getDegree()
    {
        return $this->_degree;
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

