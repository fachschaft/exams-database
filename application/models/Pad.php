<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.1
 * @todo		-
 */

class Application_Model_Pad
{
    // all stores arrays, with the id as key and the name connectet to this key as value
    /* e.g.
     * array('1' => 'Huber, Prof. Dr. A.')
     */
	
	
	// primary key
    protected $_id;

    
    // exam reffers to the examID only
    protected $_exam;
    protected $_examObject;
    protected $_etherpad_identifier;
    protected $_etherpad_link;

    protected $_created;
    protected $_uploaded_revision;
    protected $_uploaded_lastEdited;
    protected $_lastEdited;
    protected $_lastCrawled;
    
    protected $_onlineUser;
    
    
    protected $_text;
    protected $_html;
    
 	

	/**
	 * @return the $_etherpad_link
	 */
	public function getEtherpad_link() {
		return $this->_etherpad_link;
	}

	/**
	 * @param field_type $_etherpad_link
	 */
	public function setEtherpad_link($_etherpad_link) {
		$this->_etherpad_link = $_etherpad_link;
	}

	/**
	 * @return the $_examObject
	 */
	public function getExamObject() {
		return $this->_examObject;
	}

	/**
	 * @param field_type $_examObject
	 */
	public function setExamObject($_examObject) {
		$this->_examObject = $_examObject;
	}

	/**
	 * @return the $_uploaded_lastEdited
	 */
	public function getUploaded_lastEdited() {
		return $this->_uploaded_lastEdited;
	}

	/**
	 * @return the $_lastCrawled
	 */
	public function getLastCrawled() {
		return $this->_lastCrawled;
	}

	/**
	 * @param field_type $_uploaded_lastEdited
	 */
	public function setUploaded_lastEdited($_uploaded_lastEdited) {
		$this->_uploaded_lastEdited = $_uploaded_lastEdited;
	}

	/**
	 * @param field_type $_lastCrawled
	 */
	public function setLastCrawled($_lastCrawled) {
		$this->_lastCrawled = $_lastCrawled;
	}

	/**
	 * @return the $_onlineUser
	 */
	public function getOnlineUser() {
		return $this->_onlineUser;
	}

	/**
	 * @param field_type $_onlineUser
	 */
	public function setOnlineUser($_onlineUser) {
		$this->_onlineUser = $_onlineUser;
	}

	/**
	 * @return the $_lastEdited
	 */
	public function getLastEdited() {
		return $this->_lastEdited;
	}

	/**
	 * @param field_type $_lastEdited
	 */
	public function setLastEdited($_lastEdited) {
		$this->_lastEdited = $_lastEdited;
	}

	/**
	 * @return the $_text
	 */
	public function getText() {
		return $this->_text;
	}

	/**
	 * @return the $_html
	 */
	public function getHtml() {
		return $this->_html;
	}

	/**
	 * @param field_type $_text
	 */
	public function setText($_text) {
		$this->_text = $_text;
	}

	/**
	 * @param field_type $_html
	 */
	public function setHtml($_html) {
		$this->_html = $_html;
	}

	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @return the $_exam
	 */
	public function getExam() {
		return $this->_exam;
	}

	/**
	 * @return the $_etherpad_identifier
	 */
	public function getEtherpad_identifier() {
		return $this->_etherpad_identifier;
	}

	/**
	 * @return the $_created
	 */
	public function getCreated() {
		return $this->_created;
	}

	/**
	 * @return the $_uploaded_revision
	 */
	public function getUploaded_revision() {
		return $this->_uploaded_revision;
	}

	/**
	 * @param field_type $_id
	 */
	public function setId($_id) {
		$this->_id = $_id;
	}

	/**
	 * @param field_type $_exam
	 */
	public function setExam($_exam) {
		$this->_exam = $_exam;
	}

	/**
	 * @param field_type $_etherpad_identifier
	 */
	public function setEtherpad_identifier($_etherpad_identifier) {
		$this->_etherpad_identifier = $_etherpad_identifier;
	}

	/**
	 * @param field_type $_created
	 */
	public function setCreated($_created) {
		$this->_created = $_created;
	}

	/**
	 * @param field_type $_uploaded_revision
	 */
	public function setUploaded_revision($_uploaded_revision) {
		$this->_uploaded_revision = $_uploaded_revision;
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
    
   
    
}

