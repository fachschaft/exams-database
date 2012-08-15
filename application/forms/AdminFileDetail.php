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

class Application_Form_AdminFileDetail extends Zend_Form
{
	private $_id = "";
	private $_orderCount = 1;

    public $cellDecorator = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        //array('Label', array('tag' => 'td')),
    );
	
	public $cellDecoratorSingle = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );
	
	public $cellDecoratorHeader = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'th')),
    );
     
    public $rowDecoratorGroup = array(
        'FormElements',
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );
	
	private $_helperUrl;

    public function init()
    {
		$this->_helperUrl = new Zend_View_Helper_Url();
		
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');

        //$this->setAction('/exams/upload');
        
        // decorate the form
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table', 'id'=>'gradient-style')),
            'Form',
        ));
		$actions = new Zend_Form_Element_Select('action', array('value'=>'submit', 'decorators'=>$this->cellDecorator));
		$actions->setMultiOptions(array('save'=>'save', 'delete'=>'delete', 'pack'=>'pack', 'unpack'=>'unpack'));
		$this->addElement($actions, 'action');
		
		$this->addElement(new Zend_Form_Element_Submit('submit', array('value'=>'submit', 'decorators'=>$this->cellDecorator)), 'submit');

		$this->addDisplayGroup(array('action', 'submit'), 'actions', array('order'=>1000));
		$this->setDisplayGroupDecorators($this->rowDecoratorGroup);
		
    }
    
    
    public function setupDocuments($documents, $lables = array())
    {
		$lables = array('', 'ID','Display Name', 'Upload name', 'File name', 'Upload date', 'Mimetype', 'Collection', 'Reviewed', 'Downloads', 'Options');
		$this->setupHeader($lables);
		
        foreach($documents as $doc)
        {
            $checkBox = new Zend_Form_Element_MultiCheckbox('id[]', array(
															'multiOptions' => array($doc->id => $doc->id),
															'decorators' => $this->cellDecorator,
            												)
															);
			//$checkBox->setCheckedValue($doc->id);
            $this->addElement($checkBox, 'box_'.$doc->id);
			
			$fileLink = $this->_helperUrl->url(array('controller'=>'exams','action'=>'download','admin'=>$doc->id),'default',true);
			
			$this->addElement(new Custom_Form_Element_Link('link_'.$doc->id, array('value'=>$doc->displayName.'.'.$doc->extention, 'link'=>$fileLink, 'decorators' => $this->cellDecorator)));
			
			// display file name
			$this->addElement(new Zend_Form_Element_Text('display_'.$doc->id, array('value'=>$doc->displayName, 'decorators' => $this->cellDecorator)));
			
			
			$elements = array($doc->id , $doc->submitFileName , $doc->uploadDate, $doc->mimeType, $doc->collection, $doc->Reviewed, $doc->downloads);
			$i = 1;
			$groupElements = array();
			foreach($elements as $id => $elemment)
			{
				$this->addElement(new Custom_Form_Element_PlainText('text'.$i.'_'.$doc->id, array('value'=>$elemment, 'decorators' => $this->cellDecorator)));
				$groupElements[] = 'text'.$i.'_'.$doc->id;
				$i++;
			}
			
			$fileLink = $this->_helperUrl->url(array('controller'=>'exams','action'=>'download','admin'=>$doc->id),'default',true);
			
			$this->addElement(new Custom_Form_Element_Link('link_delete',
															array('value'=>'delete',
														    'decorators' => $this->cellDecorator,
															'link'=>$this->_helperUrl->url(array('controller'=>'exams-admin',
																								 'action'=>'editfiles',
																								 'id'=>$this->_id,
																								 'do'=>'delete',
																								 'file'=>$doc->id),
																								 'default',
																								 true))));
			

            //$toRender = array('box_'.$doc->id, 'text1_'.$doc->id, 'text2_'.$doc->id, 'text3_'.$doc->id, 'text4_'.$doc->id, 'text5_'.$doc->id);
			$toRender = array('box_'.$doc->id);
			$toRender = array_merge($toRender, $groupElements);
			
			// insert at position 3 the link element
			array_splice($toRender, 3, 0, 'link_'.$doc->id);
			
			array_splice($toRender, 2, 0, 'display_'.$doc->id);
			
			array_splice($toRender, 30, 0, 'link_delete');

            $this->addDisplayGroup($toRender, 'group'.$doc->id, array('order'=>$this->_orderCount++));
			
            $this->setDisplayGroupDecorators($this->rowDecoratorGroup);
        }
    }
	
	public function setupHeader($lables)
	{
		if(count($lables) != 0)
		{
			$i = 1;
			foreach($lables as $id => $elemment)
			{
				$this->addElement(new Custom_Form_Element_PlainText('text'.$i, array('value'=>$elemment, 'decorators'=>$this->cellDecoratorHeader)));
				$groupElements[] = 'text'.$i;
				$i++;
			}

            $this->addDisplayGroup($groupElements, 'header', array('order'=>0));
			$this->setDisplayGroupDecorators($this->rowDecoratorGroup);
		}
	}
	
	public function setId($id)
	{
		$this->_id = $id;
		$this->setAction('/exams-admin/editfiles/id/'.$this->_id);
	}
}

