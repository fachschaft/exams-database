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

class Application_Form_UploadFile extends Application_Form_ExamTemplate
{
	protected $_file;
	
	public $_decoratorHidden = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('class' => 'hidden_element')),
	);
	
    public function init()
    {
    	$config = Zend_Registry::get('examDBConfig');
        $this->setMethod('post');
        $this->setAction('/exams-upload/files');
          
        $this->_file = new Zend_Form_Element_File('exam_file');
        
        // Enable multifile upload for browsers that support HTML5
        $this->_file->setAttrib('multiple', true)
        			->setMultiFile($this->_config['default_upload_files_count']);
        
        $this->_file->setLabel('Uplaod Exam File:')
                ->addValidator('Count', false, array('min' => 1, 'max' => $config['max_upload_files']))
                ->addValidator('Size', false, $config['max_file_size'])
                ->addValidator('Extension', false, $config['allowed_extentions'])
                ->setAttrib('enctype', 'multipart/form-data');     
        
        $this->addElement($this->_file, 'exam_file');

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Upload abschliessen',
        	'decorators' => $this->_decoratorDivButton,
        ));
    }
    
    public function setExamId($id)
    {
        $this->addElement('hidden', 'exam', array(
            'value' => $id,
        	'decorators' => $this->_decoratorHidden,
        ));
    }

	public function setMultiFile($count)
	{
		$config = Zend_Registry::get('examDBConfig');
		if($count > $config['max_upload_files']) {
			$count = $config['max_upload_files'];
		}
		$this->_file->setMultiFile($count);
	}

}

