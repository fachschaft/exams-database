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

class Application_Model_Document
{
    protected $_extention;
    protected $_id;
    protected $_examId;
    
    protected $_exam;
    
    protected $_uploadDate;
    protected $_deleted;
    protected $_fileName;
    protected $_displayName;
    protected $_mimeType;
    protected $_submitFileName;
	protected $_checkSum;
	protected $_reviewed;
	protected $_downloads;
	protected $_collection;
    
    /**
	 * @return the $_exam
	 */
	public function getExam() {
		return $this->_exam;
	}

	/**
	 * @param field_type $_exam
	 */
	public function setExam(Application_Model_Exam $_exam) {
		$this->_exam = $_exam;
	}

	/**
	 * @return the $_collection
	 */
	public function getCollection() {
		return $this->_collection;
	}

	/**
	 * @param field_type $_collection
	 */
	public function setCollection($_collection) {
		$this->_collection = $_collection;
	}

	/**
	 * @return the $_displayName
	 */
	public function getDisplayName() {
		return $this->_displayName;
	}

	/**
	 * @param field_type $_displayName
	 */
	public function setDisplayName($_displayName) {
		$this->_displayName = $_displayName;
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
        $this->_uploadDate = (string) $date;
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

	public function setReviewed($bool)
    {
        $this->_reviewed = $bool;
        return $this;
    }
 
    public function getReviewed()
    {
        return $this->_reviewed;
    }
	
	public function setDownloads($bool)
    {
        $this->_downloads = (int) $bool;
        return $this;
    }
 
    public function getDownloads()
    {
        return $this->_downloads;
    }

}

