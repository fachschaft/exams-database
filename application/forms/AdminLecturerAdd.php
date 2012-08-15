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

class Application_Form_AdminLecturerAdd extends Zend_Form
{
	protected $_lecturer = null;
	protected $_newElement = null;
	protected $_newElementDegree = null;
	protected $_newElementFirstName = null;
	protected $_add = null;
	
    public function init()
    {
    	$this->setMethod('post');
    	 
    	
    	$this->_newElement = new Zend_Form_Element_Text('newElement');
    	$this->_newElement->setOptions(array(
    			'label'    => 'lecturer name:',
    			'required'	=> true,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    	
    	$this->_newElementDegree = new Zend_Form_Element_Text('newElementDegree');
    	$this->_newElementDegree->setOptions(array(
    			'label'    => 'degree (e.g. Prof. Dr.):',
    			'required'	=> true,
    	));
    	$this->addElement($this->_newElementDegree);
    	
    	$this->_newElementFirstName = new Zend_Form_Element_Text('newElementFirstName');
    	$this->_newElementFirstName->setOptions(array(
    			'label'    => 'first name:',
    	));
    	$this->addElement($this->_newElementFirstName); 
    	
    	
    	$this->_lecturer = new Zend_Form_Element_Multiselect('degrees');
    	 
    	$this->_lecturer->setAttrib('size', '3');
    	$this->_lecturer->setRequired(true);
    	$this->_lecturer->setAttrib('label','Select connected degrees');
    	
    	$this->setDegrees();
    	 
    	$this->addElement($this->_lecturer);
    	 
    	// Add the submit button
    	$this->_add = new Zend_Form_Element_Submit('add');
    	$this->_add->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Add',
    	));
    	$this->addElement($this->_add);
    }
    
    private function setDegrees()
    {
    	$groups = new Application_Model_DegreeMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    
    	$this->_lecturer->setMultiOptions($options);
    }
}

