<?php

class Application_Model_Document
{
    protected $_extention;
    protected $_id;
    protected $_examId;
    protected $_uploadDate;
    protected $_deleted;
    protected $_fileName;
    protected $_mimeType;
    protected $_submitFileName;
	protected $_checkSum;
    
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
            throw new Exception('Invalid property in the following model: Document');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Document');
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
    
    public function setExtention($text)
    {
        $this->_extention = (string) $text;
        return $this;
    }
 
    public function getExtention()
    {
        return $this->_extention;
    }
    
    public function setData($text)
    {
        $this->_data = (string) $text;
        return $this;
    }
 
    public function getData()
    {
        return $this->_data;
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
    
    public function setExamId($id)
    {
        $this->_examId = (int) $id;
        return $this;
    }
 
    public function getExamId()
    {
        return $this->_examId;
    }
    
    public function setUploadDate($date)
    {
        $this->_uploadDate = (int) $date;
        return $this;
    }
 
    public function getUploadDate()
    {
        return $this->_uploadDate;
    }
    
    public function setDeleteState($delet)
    {
        $this->_deleted = (bool) $delet;
        return $this;
    }
 
    public function getDeleteState()
    {
        return $this->_deleted;
    }
    
    public function setFileName($fileName)
    {
        $this->_fileName = (string) $fileName;
        return $this;
    }
 
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    public function setMimeType($type)
    {
        $this->_mimeType = (string) $type;
        return $this;
    }
 
    public function getMimeType()
    {
        return $this->_mimeType;
    } 
    
    public function setSubmitFileName($name)
    {
        $this->_submitFileName = (string) $name;
        return $this;
    }
 
    public function getSubmitFileName()
    {
        return $this->_submitFileName;
    }
	
	public function setCheckSum($sum)
    {
        $this->_checkSum = (string) $sum;
        return $this;
    }
 
    public function getCheckSum()
    {
        return $this->_checkSum;
    }

}

