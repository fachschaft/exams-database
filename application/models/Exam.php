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

class Application_Model_Exam
{
    // all stores arrays, with the id as key and the name connectet to this key as value
    /* e.g.
     * array('1' => 'Huber, Prof. Dr. A.')
     */
	
	
	// primary key
    protected $_id;
    
    // free text field
    protected $_autor;
    protected $_comment;
    
    // dates
    protected $_created;
    protected $_modified;
    
    
	protected $_degree; // uploaded degree, associated with the degree table
	protected $_status;
    protected $_semester;
    protected $_type;
    protected $_subType;
    protected $_university;
    
    protected $_writtenDegree; // exam wirtten for degree, not the uploeded degree
    
    // complex types
    protected $_lecturer;
    protected $_course;
    
    protected $_courseConnected;
    
    // more complex type
    protected $_documents; // array with docid and a document object

    
    /**
	 * @return the $_courseConnected
	 */
	public function getCourseConnected() {
		return $this->_courseConnected;
	}

	/**
	 * @param field_type $_courseConnected
	 */
	public function setCourseConnected(array $_courseConnected) {
		$this->_courseConnected = $_courseConnected;
	}

	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @return the $_autor
	 */
	public function getAutor() {
		return $this->_autor;
	}

	/**
	 * @return the $_comment
	 */
	public function getComment() {
		return $this->_comment;
	}

	/**
	 * @return the $_created
	 */
	public function getCreated() {
		return $this->_created;
	}

	/**
	 * @return the $_modified
	 */
	public function getModified() {
		return $this->_modified;
	}

	/**
	 * @return the $_degree
	 */
	public function getDegree() {
		return $this->_degree;
	}

	/**
	 * @return the $_status
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * @return the $_semester
	 */
	public function getSemester() {
		return $this->_semester;
	}

	/**
	 * @return the $_type
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * @return the $_subType
	 */
	public function getSubType() {
		return $this->_subType;
	}

	/**
	 * @return the $_university
	 */
	public function getUniversity() {
		return $this->_university;
	}

	/**
	 * @return the $_writtenDegree
	 */
	public function getWrittenDegree() {
		return $this->_writtenDegree;
	}

	/**
	 * @return the $_lecturer
	 */
	public function getLecturer() {
		return $this->_lecturer;
	}

	/**
	 * @return the $_course
	 */
	public function getCourse() {
		return $this->_course;
	}

	/**
	 * @return the $_documents
	 */
	public function getDocuments() {
		if($this->_documents == null)
			return array();
		return $this->_documents;
	}

	/**
	 * @param field_type $_id
	 */
	public function setId($_id) {
		$this->_id = (int) $_id;
	}

	/**
	 * @param field_type $_autor
	 */
	public function setAutor($_autor) {
		$this->_autor = (string) $_autor;
	}

	/**
	 * @param field_type $_comment
	 */
	public function setComment($_comment) {
		$this->_comment =  (string) $_comment;
	}

	/**
	 * @param field_type $_created
	 */
	public function setCreated($_created) {
		$this->_created = strtotime ($_created);
	}

	/**
	 * @param field_type $_modified
	 */
	public function setModified($_modified) {
		$this->_modified = strtotime ($_modified);
	}

	/**
	 * @param field_type $_degree
	 */
	public function setDegree(Application_Model_Degree $_degree) {
		$this->_degree = $_degree;
	}

	/**
	 * @param field_type $_status
	 */
	public function setStatus(Application_Model_ExamStatus $_status) {
		$this->_status = $_status;
	}

	/**
	 * @param field_type $_semester
	 */
	public function setSemester(Application_Model_Semester $_semester) {
		$this->_semester = $_semester;
	}

	/**
	 * @param field_type $_type
	 */
	public function setType(Application_Model_ExamType $_type) {
		$this->_type = $_type;
	}

	/**
	 * @param field_type $_subType
	 */
	public function setSubType(Application_Model_ExamSubType $_subType) {
		$this->_subType = $_subType;
	}

	/**
	 * @param field_type $_university
	 */
	public function setUniversity(Application_Model_ExamUniversity $_university) {
		$this->_university = $_university;
	}

	/**
	 * @param field_type $_writtenDegree
	 */
	public function setWrittenDegree(Application_Model_ExamDegree $_writtenDegree) {
		$this->_writtenDegree = $_writtenDegree;
	}

	/**
	 * @param field_type $_lecturer
	 */
	public function setLecturer(array $_lecturer) {
		$this->_lecturer = $_lecturer;
	}

	/**
	 * @param field_type $_course
	 */
	public function setCourse(array $_course) {
		$this->_course = $_course;
	}

	/**
	 * @param field_type $_documents
	 */
	public function setDocuments(array $_documents) {
		$this->_documents = $_documents;
	}
	
	/**
	 * @param array $options
	 */
	public function addCourseConnected(Application_Model_Course $new)
	{
		if($this->_courseConnected == null)
		{
			$this->_courseConnected = array($new);
		}
		else {
			array_push($this->_courseConnected, $new);
		}
	}
		
	/**
	 * @param array $options
	 */
	public function addCourse(Application_Model_Course $new)
	{
		if($this->_course == null)
		{
			$this->_course = array($new);
		}
		else {
			array_push($this->_course, $new);
		}
	}
	
	/**
	 * @param array $options
	 */
	public function addLecturer(Application_Model_Lecturer $new)
	{
		if($this->_lecturer == null)
		{
			$this->_lecturer = array($new);
		}
		else {
			array_push($this->_lecturer, $new);
		}
	}
	
	/**
	 * @param array $options
	 */
	public function addDocuments(Application_Model_Document $new)
	{
		if($this->_documents == null)
		{ 
			$this->_documents = array($new);
		}
		else {
			array_push($this->_documents, $new);
		}
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

