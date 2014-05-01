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

class Application_Model_PadMapper
{
	protected $_dbTable;
	
	
	private $_pad_key;
	private $_pad_url;
	private $_pad_baseurl;
	private $_pad_padurl;
	public $_pad_cronkey;
	
	private $_cronlimit;
	private $_crondelay;
	private $_recrawlday;
	private $_crondays;
	
	private $_elc;
	
	
	public function __construct()
	{
		$padConfig = Zend_Registry::get('pad');

		$this->_pad_key = $padConfig['apikey'];
		$this->_pad_url = $padConfig['baseurl'];
		$this->_pad_baseurl = $padConfig['baseurl'] . "/api";
		$this->_pad_padurl = $padConfig['baseurl'] . "/p/";
		$this->_pad_cronkey = $padConfig['cronkey'];
		$this->_cronlimit = $padConfig['cronlimit'];
		$this->_crondelay = $padConfig['crondelay'];
		$this->_recrawlday = $padConfig['cronrecrawldays'];
		$this->_crondays = $padConfig['crondays'];
		
		
		$path = '../library/etherpad-lite-client';
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		
		require_once ('etherpad-lite-client/etherpad-lite-client.php');
		
		$this->_elc = new EtherpadLiteClient($this->_pad_key, $this->_pad_baseurl);
	}
	
	
	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	
	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Application_Model_DbTable_Pad');
		}
		return $this->_dbTable;
	}
	
	
	public function createPad($examId, $padIdentifier)
	{		
		$result = $this->getDbTable()->getAdapter()->query("INSERT INTO  `exams-database`.`pad` (`exam_idexam` , `etherpad_identifier` ,	`created`)
													VALUES ('".$examId."',  '".$padIdentifier."', NOW());"
				);
		
		$this->_elc->createPad($padIdentifier, "type an exam protocol here ... :)");
			
	}
	
	public function deletePad($padId)
	{
		$pad = $this->fetch($padId);
		
		$result = $this->getDbTable()->getAdapter()->query("UPDATE  `exams-database`.`pad` SET  `deleted` =  '1',
		`delete_date` = NOW( ) WHERE  `pad`.`idpad` =".$padId.";");
		
		
		$this->_elc->deletePad($pad->getEtherpad_identifier());
		
	}
	
	public function getPadContent($padId)
	{
		
		$pad = $this->fetch($padId);
		
		$text = $this->_elc->getText($pad->getEtherpad_identifier());
		$text = $text->text;
		
		return $text;
	}
	
	
	public function getPadRevision($padId)
	{
	
		$pad = $this->fetch($padId);
	
		$revision = $this->_elc->getRevisionsCount($pad->getEtherpad_identifier());
		$revision = $revision->revisions;
	
		return $revision;
	}
	
	public function getLastEdited($padId)
	{
	
		$pad = $this->fetch($padId);
	
		$revision =$this->padLastEdited($pad->getEtherpad_identifier());
	
		return $revision;
	}
	
	public function padLastEdited($padIdentifier)
	{
	
		// etherpad lite return milliseconds since 1970!
		$lastEdited = $this->_elc->getLastEdited($padIdentifier);
		$lastEdited = $lastEdited->lastEdited;
		$lastEdited = floor($lastEdited / 1000);
	
		return $lastEdited;
	}
	
	public function padUsersCount($padIdentifier)
	{	
		$revision = $this->_elc->padUsersCount($padIdentifier);
		$revision = $revision->padUsersCount;
	
		return $revision;
	}
	
	public function getPadId($examId)
	{
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE  `exam_idexam` = " . $examId);
		
		$count = 0;
		$ires = array();
		foreach ($result as $res) {
			$count++;
			$ires = $res;
		}
		
		if($count == 0) {
			return -1;
		}
		
		if($count != 1) {
			throw new Exception('There multiple pads assigned to an exam');
		}
		
		return $ires['idpad'];

	}
	
	public function getPadIdByIdentifier($padID)
	{
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE  `etherpad_identifier` LIKE '" . $padID . "'");
	
		$count = 0;
		$ires = array();
		foreach ($result as $res) {
			$count++;
			$ires = $res;
		}
		
		
		if($count == 0) {
			return -1;
		}
	
		if($count != 1) {
			throw new Exception('There multiple pads assigned to an exam');
		}
	
		return $ires['idpad'];
	
	}
	
	public function fetch($padId)
	{
		$element = $this->getDbTable()->find($padId)->current();
		 
		$entry = new Application_Model_Pad();
		 
		if (count($element) != 1) {
			return $entry;
		}
	
		$entry->setId($element['idpad']);
		$entry->setExam($element['exam_idexam']);
		$entry->setEtherpad_identifier($element['etherpad_identifier']);
		$entry->setCreated($element['created']);
		$entry->setUploaded_revision($element['uploaded_revision']);
		$entry->setLastEdited(strtotime($element['uploaded_lastEdited']));

		return $entry;
	}
	
	
	
	public function uploadPad($padId, $revision, $lastEdited, $text) {
				
		//$lastEdited is in milliseconds and has to be in seconds so convert it
		$lastEdited = floor($lastEdited / 1000);		
		
		$pad = $this->fetch($padId);
		
		
		// (SECURITY) check if a new version of the pad ist available
		
		// check if the revision and the last edit donse differ
		if($lastEdited == $pad->getLastEdited() && $revision == $pad->getUploaded_revision()) {
			//var_dump($lastEdited);
			//var_dump($pad->getLastEdited());
			//var_dump($revision);
			//var_dump($pad->getUploaded_revision());
			
			echo ("The current Revision (".$revision.") is already uploaded to the Database!");
		} else {
			$em = new Application_Model_ExamMapper();
			
			$exam = $em->find($pad->getExam());
			
			$fileManger = new Application_Model_ExamFileManager ();
			
			// create file with the given text	
			$file = $fileManger->storeTmpFile($text, $revision, $exam->getId(), ".txt");
			
			
			// update database entry for the pad (update revision and uploaded date)
			$result = $this->getDbTable()->getAdapter()->query("UPDATE  `exams-database`.`pad` SET  `uploaded_revision` =  '".$revision."',
			`uploaded_lastEdited` = FROM_UNIXTIME( ".$lastEdited." ) WHERE  `pad`.`idpad` =".$padId.";");
			
			// regular upload action (move to persistent storage)
			$fileManger->storeUploadedFiles ( $file, $exam->getId() );
			
			
			// schedule the new document for revision
			$em->updateExamStatusToUnchecked ( $exam->getId() );
		}
	}
	
	// full cron, update all pads
	public function padCronFull() {
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE deleted =0");
		
		$pads = array();
		
		$count = 0;
		foreach ($result as $element) {
			$entry = new Application_Model_Pad();
			$entry->setId($element['idpad']);
			$entry->setOnlineUser($this->padUsersCount($element['etherpad_identifier']));
			$entry->setLastEdited(intval($this->padLastEdited($element['etherpad_identifier'])));
			
			$result = $this->getDbTable()->getAdapter()->query("UPDATE  `exams-database`.`pad` SET  `user_count` =  '".$entry->getOnlineUser()."',
					lastEdited = FROM_UNIXTIME(".$entry->getLastEdited()."),
			`lastCrawled` = NOW( ) WHERE  `pad`.`idpad` =".$entry->getId().";");
		}
	}
	
	// crawl all pads with any activity in the last $days, also crawl pads which are not crawled in the last $reCrawlDays
	public function padCronPartialDays($days = -1, $limit = -1, $reCrawlDays = -1) {
		
		if($limit == -1) {
			$limit = $this->_cronlimit;
		}
		
		if($reCrawlDays == -1) {
			$reCrawlDays = $this->_recrawlday;
		}
		
		if($days == -1) {
			$days = $this->_crondays;
		}
		
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE deleted =0 AND
				(lastEdited > NOW() - INTERVAL ".$days." DAY OR lastCrawled <= NOW( ) - INTERVAL ".$reCrawlDays." DAY)
				 ORDER BY lastCrawled ASC LIMIT ".$limit);
	
		$pads = array();
		
	
		$count = 0;
		foreach ($result as $element) {
			$entry = new Application_Model_Pad();
			$entry->setId($element['idpad']);
			$entry->setOnlineUser($this->padUsersCount($element['etherpad_identifier']));
			$entry->setLastEdited(intval($this->padLastEdited($element['etherpad_identifier'])));
				
			$result = $this->getDbTable()->getAdapter()->query("UPDATE  `exams-database`.`pad` SET  `user_count` =  '".$entry->getOnlineUser()."',
					lastEdited = FROM_UNIXTIME(".$entry->getLastEdited()."),
			`lastCrawled` = NOW( ) WHERE  `pad`.`idpad` =".$entry->getId().";");
			
			if($this->_crondelay != 0) {
				usleep($this->_crondelay);
			}
		}
	}
	
	// returns an array of Pad objects with all non deleted pads
	public function fetchAll($loadExamObjects = false) {
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE deleted =0");
		
		$pads = array();
		
		$count = 0;
		foreach ($result as $element) {
			$count++;
			
			$entry = new Application_Model_Pad();
			$entry->setOnlineUser($element['user_count']);
			$entry->setId($element['idpad']);
			$entry->setExam($element['exam_idexam']);
			$entry->setEtherpad_identifier($element['etherpad_identifier']);
			$entry->setCreated(strtotime($element['created']));
			$entry->setUploaded_revision($element['uploaded_revision']);
			$entry->setLastEdited(strtotime($element['lastEdited']));
			$entry->setUploaded_lastEdited(strtotime($element['uploaded_lastEdited']));
			$entry->setLastCrawled(strtotime($element['lastCrawled']));
			$entry->setEtherpad_link($this->_pad_padurl . $element['etherpad_identifier']);
			
			array_push($pads, $entry);
			
		}
		
		$examIds = array();
		foreach ($pads as $pad) {
			if(!in_array($pad->getExam(), $examIds)) {
				array_push($examIds, $pad->getExam());
			}
		}
		 
		$em = new Application_Model_ExamMapper();
		$exams = $em->fetchQuick("-1", "-1", "-1", "-1", "-1", array(), false, $examIds);
		 
		//var_dump($exams);
		 
		for ($i = 0; $i < count($pads); $i++) {
			foreach ($exams as $exam) {
				if ($pads[$i]->getExam() == $exam->getId()) {
					$pads[$i]->setExamObject($exam);
				}
			}
		
		}
	
		return $pads;
	}
	
	public function fetchActivePads($days = -1) {
		if($days == -1) {
			$days = $this->_crondays;
		}
		
		$result = $this->getDbTable()->getAdapter()->query("SELECT * FROM  `pad` WHERE deleted = 0 AND (lastEdited > NOW() - INTERVAL ".$days." DAY OR created > NOW() - INTERVAL ".$days." DAY)
				ORDER BY user_count DESC");
		
		$pads = array();
		
		$count = 0;
		foreach ($result as $element) {
			$count++;
				
			$entry = new Application_Model_Pad();
			$entry->setOnlineUser($element['user_count']);
			$entry->setId($element['idpad']);
			$entry->setExam($element['exam_idexam']);
			$entry->setEtherpad_identifier($element['etherpad_identifier']);
			$entry->setCreated(strtotime($element['created']));
			$entry->setUploaded_revision($element['uploaded_revision']);
			$entry->setLastEdited(strtotime($element['lastEdited']));
			$entry->setUploaded_lastEdited(strtotime($element['uploaded_lastEdited']));
			$entry->setLastCrawled(strtotime($element['lastCrawled']));
			$entry->setEtherpad_link($this->_pad_padurl . $element['etherpad_identifier']);
				
			array_push($pads, $entry);
				
		}
		
		$examIds = array();
		foreach ($pads as $pad) {
			if(!in_array($pad->getExam(), $examIds)) {
				array_push($examIds, $pad->getExam());
			}
		}
			
		$em = new Application_Model_ExamMapper();
		$exams = $em->fetchQuick("-1", "-1", "-1", "-1", "-1", array(), false, $examIds);
			
		//var_dump($exams);
			
		for ($i = 0; $i < count($pads); $i++) {
			foreach ($exams as $exam) {
				if ($pads[$i]->getExam() == $exam->getId()) {
					$pads[$i]->setExamObject($exam);
				}
			}
		
		}
		
		return $pads;
	}
	
}