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

class Application_Form_AdminDegreeGroupAdd extends Zend_Form
{
	protected $_group = null;
	protected $_groupSelect = null;
	protected $_groupAdd = null;
	protected $_degree = null;
	protected $_newDegree = null;

    public function init()
    {
    	$this->setMethod('post');
    	
    	$this->_newDegree = new Zend_Form_Element_Text('newElement');
    	$this->_newDegree->setOptions(array(
    			'label'    => 'new group name:',
    			'required'	=> true,
    			'validators' => array(
                    				  array('StringLength', false, array(3))),
    	));
    	$this->addElement($this->_newDegree);
    	
    	$this->_groupAdd = new Zend_Form_Element_Submit('add');
    	$this->_groupAdd->setOptions(array(
    			'ignore'   => true,
    			'label'    => 'Add',
    	));
    	$this->addElement($this->_groupAdd);
   	
    }

}

