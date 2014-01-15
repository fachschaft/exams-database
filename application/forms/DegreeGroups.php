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

class Application_Form_DegreeGroups extends Application_Form_ExamTemplate
{
    protected $_elementSelect = null;

    public function init()
    {
        $this->setMethod('post');
        $this->setAction('/exams/groups');
 
        $this->_elementSelect = new Zend_Form_Element_Select('group');
        
        $this->_elementSelect->setAttrib('size', '3');
        $this->_elementSelect->setRequired(true);
        //$this->_elementSelect->setLabel('Groups');
        $this->_elementSelect->setDecorators($this->_decoratorDivWithoutLabel);
        $this->_elementSelect->setAttrib('onclick', 'document.getElementById(\'submit\').click()');
        
        $this->setMultiOptions();
        
        $this->addElement($this->_elementSelect);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        	'decorators' =>$this->_decoratorDivButton,
        ));
        
        /*$this->setDecorators(array(
						  array('ViewScript', array(
						    'viewScript'=>'DegreeGroupsForm.phtml'
						  ),)
						));*/
        
       /* $this->clearDecorators();
        
        
        $this->setDecorators(array(
        		//'FormElements',
        		array('ViewScript', array('viewScript' => 'DegreeGroupsForm.phtml'))
        ));*/
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

         $this->_elementSelect->setMultiOptions($options);
    }

}

