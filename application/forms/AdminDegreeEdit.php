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

class Application_Form_AdminDegreeEdit extends Zend_Form
{
	protected $_degree = null;
	protected $_group = null;
	protected $_degreeSelect = null;
	protected $_degreeDelete = null;
	protected $_save = null;
	protected $_cancel = null;
	protected $_newElement = null;
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);

    public function init()
    {
    	$this->setMethod('post');
    	 
    	$this->_degree = new Zend_Form_Element_Select('select_degree');
    	 
    	$this->_degree->setAttrib('size', '1');
    	$this->_degree->setRequired(true);
    	$this->_degree->setAttrib('label','Select a group to edit');
    	
    	$this->setGroups($this->_degree);
    	 
    	$this->addElement($this->_degree);
    	
    	
    	$this->_degreeSelect = new Zend_Form_Element_Submit('select_button');
    	$this->_degreeSelect->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Edit',
    	));
    	$this->addElement($this->_degreeSelect);

    	
    	$this->_degreeDelete = new Zend_Form_Element_Submit('select_delete');
    	$this->_degreeDelete->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Delete',
    	));
    	$this->addElement($this->_degreeDelete);
    	

    }
    
    public function setDegree($degreeId)
    {
    	$this->getElement('select_degree')->setValue($degreeId);
    	
    	$this->addElement('hidden', 'select_degree_2', array(
    			'value' => $degreeId,
    			'decorators' => $this->_decoratorHidden,
    	));
    }
    
    public function showEdit($degree_name, $selectedGroup = -1)
    {
    	$this->displayEditDegree($degree_name);
    	
    	$this->displayGroups($selectedGroup);
    	
    	$this->removeElement('select_delete');
    	
    	
    	$this->getElement('select_degree')->setAttrib('disabled', 'disabled');
    	$this->getElement('select_button')->setAttrib('disabled', 'disabled');
    	
    	$this->_save = new Zend_Form_Element_Submit('select_save');
    	$this->_save->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Save',
    	));
    	$this->addElement($this->_save);
    	
    	$this->_cancel = new Zend_Form_Element_Submit('select_cancel');
    	$this->_cancel->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Cancel',
    	));
    	$this->addElement($this->_cancel);
    }
    
    public function displayEditDegree($old_name)
    {
    	$this->_newElement = new Zend_Form_Element_Text('new_degree_name');
    	$this->_newElement->setOptions(array(
    			'label'    => 'new degree name:',
    			'required'	=> true,
    			'value'		=> $old_name,
    			'validators' => array(
    					array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newElement);
    }
    
    public function displayGroups($selectedGroup)
    {
    	$this->_group = new Zend_Form_Element_Select('select_group');
    	 
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select one degree');
    	
    	$this->setDegrees($this->_group);
    	
    	$this->_group->setValue(array($selectedGroup));
    	
    	
    	 
    	$this->addElement($this->_group);
    }
    
    private function setDegrees($element)
    {
    	$degreeMapper = new Application_Model_DegreeGroupMapper();
    	$entries = $degreeMapper->fetchAll();
    	$options = array();
    
    	foreach($entries as $degree)
    	{
    		$options[$degree->getId()] = $degree->getName();
    	}
    
    	$element->setMultiOptions($options);
    }
    
    private function setGroups($element)
    {
    	$groups = new Application_Model_DegreeMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $degree)
    	{
    		$options[$degree->getId()] = $degree->name . ' - ' . $degree->group->name;
    	}
    
    	$element->setMultiOptions($options);
    }

}

