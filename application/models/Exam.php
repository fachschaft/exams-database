<?php

class Application_Model_Exam
{
    
    protected $_id;
    protected $_semester;
    protected $_type;
    protected $_subType;
    protected $_lecturer;
    protected $_documents;
    protected $_course;
    protected $_comment;
    protected $_degree;
    protected $_university;
    protected $_autor;
    
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
            throw new Exception('Invalid property in the following model: Exam');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property in the following model: Exam');
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
    
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }
 
    public function getId()
    {
        return $this->_id;
    }
    
    public function setComment($comment)
    {
        $this->_comment = (string) $comment;
        return $this;
    }
 
    public function getComment()
    {
        return $this->_comment;
    }
    
    public function setSemester($text)
    {
        $this->_semester = (string) $text;
        return $this;
    }
 
    public function getSemester()
    {
        return $this->_semester;
    }
    
    public function setType($text)
    {
        $this->_type = (string) $text;
        return $this;
    }
 
    public function getType()
    {
        return $this->_type;
    }
    
    public function setLecturer($text)
    {
        if(is_array($text)){
            $this->_lecturer = $text;
        } else {
            $this->_lecturer = array($text);
        }
        return $this;
    }
 
    public function getLecturer()
    {
        return $this->_lecturer;
    }
    
    public function setSubType($text)
    {
        $this->_subType = (string) $text;
        return $this;
    }
 
    public function getSubType()
    {
        return $this->_subType;
    }
    
    public function setCourse($text)
    {
        if(is_array($text)){
            $this->_course = $text;
        } else {
            $this->_course = array($text);
        }
            
        return $this;
    }
 
    public function getCourse()
    {
        return $this->_course;
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
    
    public function setUniversity($text)
    {
        $this->_university = (string) $text;
        return $this;
    }
 
    public function getUniversity()
    {
        return $this->_university;
    }
    
    public function setAutor($text)
    {
        $this->_autor = (string) $text;
        return $this;
    }
 
    public function getAutor()
    {
        return $this->_autor;
    }
    
    public function setDocuments($documents)
    {
        $this->_documents = $documents;
        return $this;
    }
 
    public function getDocuments()
    {
        return $this->_documents;
    }
    
    
}

