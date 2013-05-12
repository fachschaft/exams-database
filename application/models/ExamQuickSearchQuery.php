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

class Application_Model_ExamQuickSearchQuery {
	
	
	private $_corseList = array();
	private $_lecturerList = array();

	public function __construct() {
		$courses = new Application_Model_CourseMapper();
		$list =  $courses->fetchAll();
		
		foreach ($list as $item) {
			array_push($this->_corseList, $item->getName());
		}
		
		$lecturer = new Application_Model_LecturerMapper();
		$list2 =  $lecturer->fetchAll();
		
		$filter = new Zend_Filter_HtmlEntities();
		foreach ($list2 as $item) {
			if($item->getId() == 39) { //Rüschendorf
				//var_dump($item->getName());
				//die(htmlentities(($item->getName())));
			}
			
			//Becker, Prof. Dr. B.
			//var_dump(htmlentities($item->getName()) . " , " . $item->getDegree());
			//die(htmlentities($item->getName()) + " , " + $item->getDegree());
			array_push($this->_lecturerList, htmlentities($item->getName()) . ", " . $item->getDegree() . " " . $item->getFirstName());
		}
		
	}
	
	public function getResults($term) {
		
		// ToDo: Build a nice matching function, e.G. informatik III should match to informatik 3 ... maybe do a weighting on the search items 
		
		
		// also a special char in the search should match to the name e.G. ü = &uuml;
		$filter = new Zend_Filter_HtmlEntities();
		$term = utf8_decode ($term);
		
		// append all the lists
		$filter_array = array_merge($this->_corseList, $this->_lecturerList);
		
		if (strlen(html_entity_decode($term, ENT_QUOTES, 'ISO-8859-15')) >= 1) {
			$filter = function($elements) use ($term)
			{
				if(stristr($elements,$term))
					return true;
				return false;
			};
			
			$res_arr = array();
			foreach (array_filter($filter_array,$filter) as $res) {
				array_push($res_arr, html_entity_decode($res));
			}
			
			return $res_arr;
		}
		
		return array();
	}

	
}