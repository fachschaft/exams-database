<?php 
 /**
 * exams-database
 * @copyright   Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo        -
 */

class Application_Form_AdminDegreeAdd extends Zend_Form
{
	protected $_group = null;
	protected $_newElement = null;
	protected $_add = null;

    public function init()
    {
        $this->setMethod('post');
    	
        
        $this->_newElement = new Zend_Form_Element_Text('newElement');
        $this->_newElement->setOptions(array(
        		'label'    => 'new degree name:',
        		'required'	=> true,
        		'validators' => array(
        				array('StringLength', false, array(3))),
        ));
        $this->addElement($this->_newElement);
        
        
    	$this->_group = new Zend_Form_Element_Select('group');
    	
    	$this->_group->setAttrib('size', '3');
    	$this->_group->setRequired(true);
    	$this->_group->setAttrib('label','Select a group');
    	    	
    	$this->setMultiOptions();
    	
    	$this->addElement($this->_group);
    	
    	// Add the submit button
    	$this->_add = new Zend_Form_Element_Submit('add');
    	$this->_add->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Add',
    	));
    	$this->addElement($this->_add);
    }
    
    public function setMultiOptions()
    {
    	$groups = new Application_Model_DegreeGroupMapper();
    	$entries = $groups->fetchAll();
    
    	$options = array();
    
    	foreach($entries as $group)
    	{
    		$options[$group->getId()] = $group->getName();
    	}
    
    	$this->_group->setMultiOptions($options);
    }


}

