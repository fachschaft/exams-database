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

class Application_Form_ExamDegrees extends Application_Form_ExamTemplate
{
    protected $_elementSelect = null;
    
    public $_decoratorHidden = array(
    		 'ViewHelper',
    		array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
    );

    public function init()
    {
        $this->setMethod('post');
         $this->setAction('/exams/degrees');
 
        //
        $this->_elementSelect = new Zend_Form_Element_Select('degree');
        $this->_elementSelect->setAttrib('size', '4')
                             ->setRequired(true)
                             ->setLabel('Degree')
        					 ->setDecorators($this->_decoratorDiv);
        $this->addElement($this->_elementSelect);
        
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Weiter',
        	'decorators' =>$this->_decoratorDivButton,
        ));
    }
    
    public function setMultiOptions($group)
    {
        $degrees = new Application_Model_DegreeMapper();
        $entries = $degrees->fetchByGroup($group);  
        $options = array(); 
        
        foreach($entries as $gr)
        {
            $options[$gr->getId()] = $gr->getName();
        }

        $this->_elementSelect->setMultiOptions($options);
    }
    
    public function setGroup($id)
    {
        $this->addElement('hidden', 'group', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }

}

