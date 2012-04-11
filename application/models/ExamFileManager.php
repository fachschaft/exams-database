<?php

class Application_Model_ExamFileManager
{
	private $_fileDestinationPath;
	private $_fileTempPath;
	
	public function __construct()
    {
        $config = Zend_Registry::get('examDBConfig');

		if($config['storagepath'] != "") {
			$this->_fileDestinationPath = $config['storagepath'];
		}
		// if not end with a /, add one
		if(substr($config['storagepath'], -1) != "/") { $this->_fileDestinationPath .= "/"; }
			
		if($config['temppath'] != "") {
			$this->_fileTempPath = $config['temppath'];
		}
		// if not end with a /, add one
		if(substr($config['temppath'], -1) != "/") { $this->_fileTempPath .= "/"; }
		
		// check if destination path is writable
		if(!is_writable($this->_fileDestinationPath))
		{
			throw new Zend_Exception ("Cannot write in directory ".$this->_fileDestinationPath."! Plese call your admin.", 500);
		}
		
		// check if temp path is writable
		if(!is_writable($this->_fileTempPath))
		{
			throw new Zend_Exception ("Cannot write in directory ".$this->_fileTempPath."! Plese call your admin.", 500);
		}
		
    }
	
	public function packDocuments($documents)
	{
		$zip = new ZipArchive;
		$extention = ".zip"; //extetion for the new archive | hase to start with a dot
		
		// get free filename in temp dir
		$new_file_name = $this->getFreeFilename($this->_fileTempPath);
		$tmp_filename = $this->_fileTempPath.$new_file_name;

		if ($zip->open($tmp_filename, ZIPARCHIVE::CREATE) === TRUE) {
			
			foreach($documents as $doc) {
				// check if file exists
				if(file_exists($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention)) {
					// adds a file to the zip archive
					$zip->addFile($this->_fileDestinationPath.$doc->fileName.'.'.$doc->extention, $doc->id .'_'. $doc->submitFileName);
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
						$document->fileName = $new_file_name;
						$document->mimeType = mime_content_type($destination_filename);
						$document->ExamId = $doc->examId;
						$document->CheckSum = md5_file($destination_filename);
						
						$documentMapper = new Application_Model_DocumentMapper();
						$documentMapper->saveNew($document);
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
						
				
				// get the extetion of the filename from the archive
				$ex_names = preg_split('/\./', $info['name'], -1);
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
						$document->submitFileName = $info['name'];
						$document->fileName = $new_file_name;
						$document->mimeType = mime_content_type($destination_filename);
						$document->ExamId = $doc->examId;
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
			
			// get the extetion of the filename from the archive
			$ex_names = preg_split('/\./', $e->getName(), -1);
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
					$document->submitFileName = $e->getName();
					$document->fileName = $new_file_name;
					$document->mimeType = mime_content_type($destination_filename);
					$document->ExamId = $doc->examId;
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
		
		$rar_arch->close();

	}
	
	public function storeUploadedFiles($files, $examId)
	{
		if(!is_array($files)) {
			$files = array($files);
		}
	
		foreach($files as $location) 
		{
			$ex_names = preg_split('/\./', $location, -1);
			$extention = $ex_names[count($ex_names)-1];
			
			if(!is_writable($this->_fileDestinationPath)) {
				unlink($location);
				throw new Zend_Exception ("Cannot write in directory (".$this->_fileDestinationPath.")", 500);
			}
			
			$sum = md5_file($location);
			$mime = mime_content_type($location);
			
			// move the file to destination
			$new_file_name = $this->getFreeFilename($this->_fileDestinationPath, $extention);
			rename($location, $this->_fileDestinationPath.$new_file_name.".".$extention);
			
			// save the file name to database (crate a document) and link this to the exam
			$document = new Application_Model_Document();

			$document->ExamId = $examId;
			$document->extention = $extention;
			$file_names = preg_split('/\//', $location, -1);
			$document->submitFileName = $file_names[count($file_names)-1];
			$document->fileName = $new_file_name;
			$document->mimeType = $mime;
			$document->checkSum = $sum;
			
			$documentMapper = new Application_Model_DocumentMapper();
			$documentMapper->saveNew($document);
		}
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

