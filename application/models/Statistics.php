<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultaet Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.1
 * @since		1.1
 * @todo		-
 */

class Application_Model_Statistics {
	
	public function getExamUploads($year = -1) {
		/* SELECT  count(*), DATE_FORMAT(create_date, '%Y-%m')  as order_date FROM `exam`
		 GROUP BY order_date
		Order by order_date
		*/
		
		$dbTable = new Application_Model_DbTable_Exam();
		
		$where = "";
		if(isset($year)) {
			$where = "WHERE YEAR(create_date) = " . $year;
		}
		
		$res = $dbTable
		->getDefaultAdapter()
		->query("SELECT  count(*) as uploads, DATE_FORMAT(create_date, '%Y-%m')  as order_date, DATE_FORMAT(create_date, '%m') as month, DATE_FORMAT(create_date, '%Y') as year FROM `exam` ".$where." GROUP BY order_date Order by order_date")
		->fetchAll()
		;
		
		$res_array[] = array();
		$res_array['data'] = array();
		$res_array['axis'] = array();
		
		foreach ($res as $row2) {
			$res_array['data'][] = $row2['uploads'];
			$res_array['axis'][] = $row2['order_date'];
			
		}

		return $res_array;
	}
	
	
	public function getExamUploadsYear($year = -1) {
		/* SELECT  count(*), DATE_FORMAT(create_date, '%Y-%m')  as order_date FROM `exam`
		 GROUP BY order_date
		Order by order_date
		*/
		
		// this should not happen!
		if($year == -1) {
			throw new Exception('Loaded year statistic with a wrong year('.$year.')');
		}
	
		$dbTable = new Application_Model_DbTable_Exam();
	
		$where = "";
		if(isset($year)) {
			$where = "WHERE YEAR(create_date) = " . $year;
		}
	
		$res = $dbTable
		->getDefaultAdapter()
		->query("SELECT  count(*) as uploads, DATE_FORMAT(create_date, '%Y-%m')  as order_date, DATE_FORMAT(create_date, '%m') as month, DATE_FORMAT(create_date, '%Y') as year FROM `exam` ".$where." GROUP BY order_date Order by order_date")
		->fetchAll()
		;
		
		$res_array = array();
		
		for ($i = 0; $i <= 11; $i++) {
			foreach ($res as $row2) {
				if(intval($row2['month']) == $i+1) { $res_array[$i] =  $row2['uploads']; }
			}
		}
		
		for ($i = 0; $i <= 11; $i++) {
			if(!isset($res_array[$i])) {
				$res_array[$i] = "";
			}
		}
		
		ksort($res_array);
	
		return $res_array;
	}
	
}