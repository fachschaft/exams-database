<?php 
/**
 * exams-database
 * @copyright	Written for Fachschaft Technische Fakultät Freiburg, Germany and licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.
 * @link		https://github.com/aritas1/exams-database/
 * @author		Daniel Leinfelder <mail@aritas.de>
 * @author		William Glover <william@aamu-uninen.de>
 * @version		1.0
 * @since		1.0
 * @todo		-
 */

class Application_Model_ExamFileManager
{
	private $_fileDestinationPath;
	private $_fileTempPath;
	
	public function getFileDestinationPath() {
		return $this->_fileDestinationPath;
	}
	
	public function getFileStoragePath() {
		return $this->_fileDestinationPath;
	}

	public function getFileTempPath() {
		return $this->_fileTempPath;
	}
	
	
	public function setFileDestinationPath($fileDestinationPath) {
		if (strlen ( $fileDestinationPath ) == 0)
			throw new Exception ( 'The storage path is empty. Please check your application.ini' );

		// if the path does not end with a /, add one
		if (substr ( $fileDestinationPath, - 1 ) != "/") {
			$fileDestinationPath .= "/";
		}
		
		$this->_fileDestinationPath = $fileDestinationPath;
	}

	public function setFileTempPath($fileTempPath) {
		if (strlen ( $fileTempPath ) == 0)
			throw new Exception ( 'The temporary storage path is empty. Please check your application.ini' );
			
		// if the path does not end with a /, add one
		if (substr ( $fileTempPath, - 1 ) != "/") {
			$fileTempPath .= "/";
		}
		$this->_fileTempPath = $fileTempPath;
	}

	public function __construct()
    {
		$config = Zend_Registry::get ( 'examDBConfig' );
		
		$this->setFileDestinationPath ( $config ['storagepath'] );
		$this->setFileTempPath ( $config ['temppath'] );
		
		// check if the set paths are writable
		if(!is_writable($this->_fileDestinationPath))
			throw new Zend_Exception ("Cannot write in directory ".$this->_fileDestinationPath."! Plese call your admin.", 500);
		
		if(!is_writable($this->_fileTempPath))
			throw new Zend_Exception ("Cannot write in directory ".$this->_fileTempPath."! Plese call your admin.", 500);
    }
	
	public function downloadDocuments($id) {
		$doc = new Application_Model_DocumentMapper ();
		$entries = $doc->fetch ( $id );
		
		if ($entries->DeleteState)
			throw new Exception ( 'The document is not longer available', 500 );
		
		$doc->updateDownloadCounter ( $entries->id );
		
		header ( 'Content-Type: application/octet-stream' );
		header ( "Content-Disposition: attachment; filename=" . date ( 'YmdHis' ) . "." . $entries->getExtention () );
		
		$path = $this->getFileStoragePath ();
		
		if(!file_exists($path)) throw new Exception ( 'dircetory not found', 404 );
		if(!is_readable($path))	throw new Exception ( 'no access rights for the configured directory', 403 );
		
		$file = $path . $entries->getFileName () ;//. "." . $entries->getextention ();
		
		if(!file_exists($file)) throw new Exception ( 'File not found', 404 );
		if(!is_readable($file))	throw new Exception ( 'no access rights', 403 );

		readfile ( $file );
	}
    

	public function packDocuments($documents)
	{
		$zip = new ZipArchive;
		$extention = ".zip"; //extetion for the new archive | has to start with a dot
		
		// get free filename in temp dir
		$new_file_name = $this->getFreeFilename($this->_fileTempPath);
		$tmp_filename = $this->_fileTempPath.$new_file_name;

		if ($zip->open($tmp_filename, ZIPARCHIVE::CREATE) === TRUE) {
			
			foreach($documents as $doc) {
				// check if file exists
				if(file_exists($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention)) {
					// adds a file to the zip archive
					$zip->addFile($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention, $doc->id .'_'. $doc->displayName.'.'.$doc->extention);
				} else {
					// if add file is not existing, delete temp archive and drop a exception
					$zip->close();
					unlink($tmp_filename);
					throw new Exception($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention . ' file not exist. Error while packing.', 500);
				}
			}
			$zip->close();
			
			// archive packed
			if($zip->status == ZIPARCHIVE::ER_OK) {
				// get free file name in destionation
				$new_file_name = $this->getFreeFilename($this->_fileDestinationPath, $extention);
				$destination_filename = $this->_fileDestinationPath.$new_file_name.$extention;
				
				// check if the new destination dont contain a file with this name
				if(!file_exists($destination_filename)) {
					rename($tmp_filename, $destination_filename);
					
					// check if rename was successful
					if(file_exists($destination_filename)) {
						// save the new document
						$document = new Application_Model_Document();
						$document->extention = substr($extention, 1); // trim the leading dot
						$document->submitFileName = $new_file_name.$extention;
						$document->fileName = $new_file_name.$extention;
						$document->mimeType = mime_content_type($destination_filename);
						$document->ExamId = $doc->examId;
						$document->CheckSum = md5_file($destination_filename);
						$document->displayName = 'Collection'.$doc->examId;
						
						$documentMapper = new Application_Model_DocumentMapper();
						$document->id = $documentMapper->saveNew($document);
						
						$document->displayName = 'Collection_'.$document->id;
						
						
						$documentMapper->markDocumentToCollection($document);
						$documentMapper->updateDisplayName($document);
					}
					
				} else { 
					// destination already contains this file
					unlink($tmp_filename);
					throw new Exception($destination_filename . ' destination file already exists.', 500); 
				}
			} else {
				// packing faild
				throw new Exception($this->zipFileErrMsg($zip->status), 500);
			}
		} else {
			throw new Exception('Cant open zip archive', 500);
		}
	}
	
	
	public function unpackDocuments($documents)
	{
		foreach($documents as $doc)
		{
			switch($doc->mimeType)
			{
				case"application/zip":
					$this->unpackZipArchive($doc);
				break;
				case"application/x-rar":
					$this->unpackRarArchive($doc);
					break;
				case"application/x-gzip":
					//.gz
					break;
				default:
					// this is no file how can be unpacked
					//echo "<p>this is no file how can be unpacked</p>";
				break;
			}
		}
	}
	
	
	private function unpackZipArchive($doc)
	{
		$zip = new ZipArchive;
		if ($zip->open($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention) === TRUE)
		{
			// go through all files in the archive
			for ($i=0; $i<$zip->numFiles;$i++) {
				// get all infos
				$info = $zip->statIndex($i);
				// get filepointer
				$fp = $zip->getStream($info['name']);
				
				// get a free file name in temp folder
				$new_file_name = $this->getFreeFilename($this->_fileTempPath);
				$tmp_filename = $this->_fileTempPath.$new_file_name;

				// create a file from the filepoint of the zip archive
				if(file_put_contents($tmp_filename, $fp) == false) { throw new Zend_Exception ("Cannot extract file.", 500); }
						
				
				$this->storeUploadedFile($tmp_filename, $doc->examId, $info['name']);
			}
		} else {
			// unpacking faild
			throw new Exception('Can\'t open zip Archive. ' . $this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention, 500);  
		}
	}
	
	private function unpackRarArchive($doc)
	{
		$rar_arch = RarArchive::open($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention);
		if ($rar_arch === FALSE)
			throw new Zend_Exception ("Failed opening file.", 500);
		
		$entries = $rar_arch->getEntries();
		if ($entries === FALSE)
			throw new Zend_Exception ("Failed fetching entries.", 500);
		
		foreach ($entries as $e) {
			$new_file_name = $this->getFreeFilename($this->_fileTempPath);
			$tmp_filename = $this->_fileTempPath.$new_file_name;
			
			$stream = $e->getStream();
			if ($stream === FALSE)
				throw new Zend_Exception ("Failed opening first file.", 500);
			
			if(file_put_contents($tmp_filename, $stream) == false) {
				throw new Zend_Exception ("Cannot extract file.", 500);
			}
			
			$this->storeUploadedFile ( $tmp_filename, $doc->examId, $e->getName());

		}
		
		$rar_arch->close();

	}
	
	
	public function storeUploadedFiles($files, $examId)
	{
		if(!is_array($files)) {
			$files = array($files);
		}
	
		foreach($files as $location)
		{
			$file_names = preg_split('/\//', $location, -1);
			$file_name = $file_names[count($file_names)-1];
			$this->storeUploadedFile ($location, $examId, $file_name);
		}
	}
	
	/**
	 * @param tmp_filename
	 * @param document
	 * @param documentMapper
	 */private function storeUploadedFile($tmp_filename, $examId, $fileName) {
		// get the extetion of the filename from the archive
		$ex_names = preg_split('/\./', $fileName, -1);
		$extention = $ex_names[count($ex_names)-1];
		
		// get free filename from destionation
		$new_file_name = $this->getFreeFilename($this->_fileDestinationPath);
		$destination_filename = $this->_fileDestinationPath.$new_file_name.'.'.$extention;
		if(!file_exists($destination_filename)) {
			// moving file to destionation
			rename($tmp_filename, $destination_filename);
				
			// check if rename was successful
			if(file_exists($destination_filename)) {
				// save the new file
				$document = new Application_Model_Document();
				$document->extention = $extention;
				$document->submitFileName = $fileName;
				$document->fileName = $new_file_name.'.'.$extention;
				$document->mimeType = mime_content_type($destination_filename);
				$document->ExamId = $examId;
				$document->CheckSum = md5_file($destination_filename);
		
				$documentMapper = new Application_Model_DocumentMapper();
				$documentMapper->saveNew($document);
			}
		} else {
			// destination already contains this archive file
			unlink($tmp_filename);
			throw new Exception($destination_filename . ' destination file already exists.', 500);
		}
	}
	
	public function resetAllMimeTypesInDatabese()
	{
		echo "<p>resetAllMimeTypesInDatabese</p>";
		
		$documentMapper = new Application_Model_DocumentMapper();
		
		$docDb = new Application_Model_DbTable_Document();
		
		$data = $docDb->fetchAll();
		
		
		foreach ($data as $doc)
		{
			$tmpDoc = $documentMapper->fetch($doc['iddocument']);
			
			if(!file_exists($this->getFileStoragePath() . $tmpDoc->getFileName()))
			{
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()." dose not exists on the file system! id: ".$tmpDoc->id."</p>";
				continue;
			}
			
			if(!is_readable($this->getFileStoragePath() . $tmpDoc->getFileName()))
			{
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()."  is not readable! id: ".$tmpDoc->id."</p>";
				continue;
			}
			
			$tmpDoc->setMimeType(mime_content_type($this->getFileStoragePath() . $tmpDoc->getFileName()));
			$documentMapper->updateMimeType($tmpDoc);
			
		}
	}
	
	public function checkAllFilesExistsAndReadable()
	{
		echo "<p>checkAllFilesExistsAndReadable</p>";
		
		$documentMapper = new Application_Model_DocumentMapper();
		
		$docDb = new Application_Model_DbTable_Document();
		
		$data = $docDb->fetchAll();
		
		
		foreach ($data as $doc)
		{
			$tmpDoc = $documentMapper->fetch($doc['iddocument']);
			
			if(!file_exists($this->getFileStoragePath() . $tmpDoc->getFileName()))
			{
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()." dose not exists on the file system! id: ".$tmpDoc->id."</p>";
				continue;
			}
				
			if(!is_readable($this->getFileStoragePath() . $tmpDoc->getFileName()))
			{
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()."  is not readable! id: ".$tmpDoc->id."</p>";
				continue;
			}
		}
	}
	
	
	public function checkFilesMD5()
	{
		echo "<p>checkFilesMD5</p>";
	
		$documentMapper = new Application_Model_DocumentMapper();
	
		$docDb = new Application_Model_DbTable_Document();
	
		$data = $docDb->fetchAll();
	
		$diffs = 0;
		foreach ($data as $doc)
		{
			$tmpDoc = $documentMapper->fetch($doc['iddocument']);
				
			
			$md5Now = md5_file($this->getFileStoragePath() . $tmpDoc->getFileName());
			
			if($md5Now != $tmpDoc->getCheckSum())
			{
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()." ha a different check sum stored in the database (". $tmpDoc->getCheckSum() ."), id: ".$tmpDoc->id."</p>";
			}
			
		}
		
		echo "<p>" . $diffs . " file(s) are damaged?</p>";
	}
	
	public function restorMD5SumIfMising()
	{
		echo "<p>checkFilesMD5</p>";
	
		$documentMapper = new Application_Model_DocumentMapper();
	
		$docDb = new Application_Model_DbTable_Document();
	
		$data = $docDb->fetchAll();
	
		$updated = 0;
		foreach ($data as $doc)
		{
			$tmpDoc = $documentMapper->fetch($doc['iddocument']);
			
			if($tmpDoc->getCheckSum() == NULL)
			{
				$md5Now = md5_file($this->getFileStoragePath() . $tmpDoc->getFileName());
				$docDb->update(array('md5_sum' => $md5Now), 'iddocument = ' .$doc['iddocument']);
				
				echo "<p> ".$this->getFileStoragePath() . $tmpDoc->getFileName()." store new check sum (". $md5Now ."), id: ".$tmpDoc->id."</p>";
				$updated++;
			}
				
		}
		echo "<p>" . $updated . " file(s) md5 sums updated</p>";
	}
	
	
	
	
	////////////// HELPER
	
	public function getFreeFilename($path, $extention = "")
	{
		$new_file_name = md5(time()+rand());
		$count = 0;
		while(file_exists($path.$new_file_name.$extention))
		{
			$new_file_name = md5(time()+rand());
			$count++;
			if($count > 1000) { throw new Zend_Exception ("Cannot find a free filname, please contact the admin!"); }
		}
		
		return $new_file_name;
	}
	
	public function zipFileErrMsg($errno) { 
	  // using constant name as a string to make this function PHP4 compatible 
	  $zipFileFunctionsErrors = array( 
		'ZIPARCHIVE::ER_MULTIDISK' => 'Multi-disk zip archives not supported.', 
		'ZIPARCHIVE::ER_RENAME' => 'Renaming temporary file failed.', 
		'ZIPARCHIVE::ER_CLOSE' => 'Closing zip archive failed', 
		'ZIPARCHIVE::ER_SEEK' => 'Seek error', 
		'ZIPARCHIVE::ER_READ' => 'Read error', 
		'ZIPARCHIVE::ER_WRITE' => 'Write error', 
		'ZIPARCHIVE::ER_CRC' => 'CRC error', 
		'ZIPARCHIVE::ER_ZIPCLOSED' => 'Containing zip archive was closed', 
		'ZIPARCHIVE::ER_NOENT' => 'No such file.', 
		'ZIPARCHIVE::ER_EXISTS' => 'File already exists', 
		'ZIPARCHIVE::ER_OPEN' => "Cant open file", 
		'ZIPARCHIVE::ER_TMPOPEN' => 'Failure to create temporary file.', 
		'ZIPARCHIVE::ER_ZLIB' => 'Zlib error', 
		'ZIPARCHIVE::ER_MEMORY' => 'Memory allocation failure', 
		'ZIPARCHIVE::ER_CHANGED' => 'Entry has been changed', 
		'ZIPARCHIVE::ER_COMPNOTSUPP' => 'Compression method not supported.', 
		'ZIPARCHIVE::ER_EOF' => 'Premature EOF', 
		'ZIPARCHIVE::ER_INVAL' => 'Invalid argument', 
		'ZIPARCHIVE::ER_NOZIP' => 'Not a zip archive', 
		'ZIPARCHIVE::ER_INTERNAL' => 'Internal error', 
		'ZIPARCHIVE::ER_INCONS' => 'Zip archive inconsistent', 
		'ZIPARCHIVE::ER_REMOVE' => "Can't remove file", 
		'ZIPARCHIVE::ER_DELETED' => 'Entry has been deleted', 
	  ); 
	  $errmsg = 'unknown'; 
	  foreach ($zipFileFunctionsErrors as $constName => $errorMessage) { 
		if (defined($constName) and constant($constName) === $errno) { 
		  return 'Zip File Function error: '.$errorMessage; 
		} 
	  } 
	  return 'Zip File Function error: unknown'; 
	}


}

