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

class StatisticsController extends Zend_Controller_Action
{
	private $_authManager;

    public function init()
    {	
    	$this->_authManager = new Application_Model_AuthManager();
		// check if a login exists for admin controller
		if ((!$this->_authManager->isAllowed(null, 'view_admin_interface'))) {
			$data = $this->getRequest ()->getParams ();
			// save the old controller and action to redirect the user after the login
			$authmanager = new Application_Model_AuthManager ();
			$data = $authmanager->pushParameters ( $data );
			
			$this->_helper->Redirector->setGotoSimple ( 'index', 'login', null, $data );
		
		}
		
		$this->view->jQuery()->enable();
		$this->view->jQuery()->uiEnable();
		
		//
    }

    public function indexAction()
    {
		// action body
    }


    public function uploadAction()
    {
    	$stats = new Application_Model_Statistics();
    	
    	// define years
    	$this->view->upload_years = $stats->getEaxamAllUsedYears();

    	
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	
    }
    
    public function uploadTotalAction()
    {    	
    	// this function is for ajax polling
    	
    	require_once ('jpgraph/jpgraph.php');
    	
    	$stats = new Application_Model_Statistics();
    	 
    	// define years
    	$this->view->upload_years = $stats->getEaxamAllUsedYears();
    
    	 
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	 
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	
    	$group = false;
    	$degree = -1;
    	
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	$months = $gDateLocale->GetShortMonth();
    	$data1y = $stats->getExamUploadsYear($year, $degree, $group);
    	
    	for ($i = 0; $i < sizeof($data1y); $i++) {
    		if($data1y[$i] == "") { $data1y[$i] = 0; }
    		echo($months[$i] . " - " . $data1y[$i] . "<br>");
    	}
    	
    	exit();
    	 
    	 
    }
    
    
    public function courseAddDateAction()
    {
    	if(!$this->_authManager->isAllowed(null, 'modify_course'))
    		throw new Custom_Exception_PermissionDenied("Permission Denied");
    	
    	$request = $this->getRequest();
    	
    	$course = "-1";
    	$date = 0;
    	$comment = "";
    	
    	if(isset($request->course) && isset($request->date)) {
    		if($request->date == "") {
    			throw new Exception("No date given");
    		}
    		$course = $request->course;
    		$date = $request->date;
    		if(isset($request->comment)) {
    			$comment = $request->comment;
    		}
    	} else {
    		throw new Exception("No date given");
    	}
    	
    	$dates = date("Y-m-d", strtotime($date));
    	
    	
    	$ams = new Application_Model_Statistics();
    	
    	$ams->addCourseExamination($course, $dates, $comment);
    	
    	$result = 'ok';
    	$this->_helper->json($result);
    	exit();
    	
    }
    
    public function graphUploadTotal2Action()
    {
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    	
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	
    	//bar1
    	$data1y=array(115,130,135,130,110,130,130,150,130,130,150,120);
    	//bar2
    	$data2y=array(180,200,220,190,170,195,190,210,200,205,195,150);
    	//bar3
    	$data3y=array(220,230,210,175,185,195,200,230,200,195,180,130);
    	$data4y=array(40,45,70,80,50,75,70,70,80,75,80,50);
    	$data5y=array(20,20,25,22,30,25,35,30,27,25,25,45);
    	//line1
    	$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    	foreach ($data6y as &$y) { $y -=10; }
    	
    	// Create the graph. These two calls are always required
    	$graph = new Graph(750,320,'auto');
    	$graph->SetScale("textlin");
    	$graph->SetY2Scale("lin",0,90);
    	$graph->SetY2OrderBack(false);
    	
    	$graph->SetMargin(35,50,20,5);
    	
    	$theme_class = new UniversalTheme;
    	$graph->SetTheme($theme_class);
    	
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));
    	
    	$months = $gDateLocale->GetShortMonth();
    	$months = array_merge(array_slice($months,3,9), array_slice($months,0,3));
    	$graph->SetBox(false);
    	
    	$graph->ygrid->SetFill(false);
    	$graph->xaxis->SetTickLabels(array('A','B','C','D'));
    	$graph->yaxis->HideLine(false);
    	$graph->yaxis->HideTicks(false,false);
    	// Setup month as labels on the X-axis
    	$graph->xaxis->SetTickLabels($months);
    	
    	// Create the bar plots
    	$b1plot = new BarPlot($data1y);
    	$b2plot = new BarPlot($data2y);
    	
    	$b3plot = new BarPlot($data3y);
    	$b4plot = new BarPlot($data4y);
    	$b5plot = new BarPlot($data5y);
    	
    	//$lplot = new LinePlot($data6y);
    	
    	// Create the grouped bar plot
    	$gbbplot = new AccBarPlot(array($b3plot,$b4plot,$b5plot));
    	//$gbplot = new GroupBarPlot(array($b1plot,$b2plot,$gbbplot));
    	
    	// ...and add it to the graPH
    	$graph->Add($gbbplot);
    	//$graph->AddY2($lplot);
    	
    	$b1plot->SetColor("#0000CD");
    	$b1plot->SetFillColor("#0000CD");
    	$b1plot->SetLegend("Cliants");
    	
    	$b2plot->SetColor("#B0C4DE");
    	$b2plot->SetFillColor("#B0C4DE");
    	$b2plot->SetLegend("Machines");
    	
    	$b3plot->SetColor("#8B008B");
    	$b3plot->SetFillColor("#8B008B");
    	$b3plot->SetLegend("First Track");
    	
    	$b4plot->SetColor("#DA70D6");
    	$b4plot->SetFillColor("#DA70D6");
    	$b4plot->SetLegend("All");
    	
    	$b5plot->SetColor("#9370DB");
    	$b5plot->SetFillColor("#9370DB");
    	$b5plot->SetLegend("Single Only");
    	
    	/*$lplot->SetBarCenter();
    	$lplot->SetColor("yellow");
    	$lplot->SetLegend("Houses");
    	$lplot->mark->SetType(MARK_X,'',1.0);
    	$lplot->mark->SetWeight(2);
    	$lplot->mark->SetWidth(8);
    	$lplot->mark->setColor("yellow");
    	$lplot->mark->setFillColor("yellow");*/
    	
    	$graph->legend->SetFrameWeight(1);
    	$graph->legend->SetColumns(6);
    	$graph->legend->SetColor('#4E4E4E','#00A78A');
    	
    	/*$band = new PlotBand(VERTICAL,BAND_RDIAG,11,"max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);*/
    	
    	$graph->title->Set("Combineed Line and Bar plots");
    	
    	// Display the graph
    	$graph->Stroke();
    	
    	exit();
    }

    public function graphUploadTotalAction()
	{
		$stats = new Application_Model_Statistics();
			
		
		$path = '../library/jpgraph';
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		
    	require_once ('jpgraph/jpgraph.php');
		require_once ('jpgraph/jpgraph_bar.php');
		
		
		
		
		$request = $this->getRequest ();
		if (isset ( $request->year )) {
			$year = $request->year;
		} else {
			$year = date("Y");
		}
		
		$group = false;
		$degree = -1;
		
		if (isset ( $request->group )) {
			$degree=$request->group;
			$group = true;
		}
		
		if (isset ( $request->degree )) {
			$degree = $request->degree;
		}
		
		$months = $gDateLocale->GetShortMonth();
		$data1y = $stats->getExamUploadsYear($year, $degree, $group);
		
		
		//$data1y=array(47,80,40,116);
		$data2y=array(61,30,82,105);
		$data3y=array(115,50,70,93);
		
		
		// Create the graph. These two calls are always required
		$graph = new Graph(700,300,'auto');
		$graph->SetScale("textint");
		
		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		
		//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
		$graph->SetBox(false);
		
		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($months);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		// Create the bar plots
		$b1plot = new BarPlot($data1y);
		
		// Create the grouped bar plot
		$gbplot = new GroupBarPlot(array($b1plot));
		// ...and add it to the graPH
		$graph->Add($gbplot);
		
		
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#cc1111");
		
		/*$b2plot->SetColor("white");
		$b2plot->SetFillColor("#11cccc");
		
		$b3plot->SetColor("white");
		$b3plot->SetFillColor("#1111cc");*/
		$sum = 0;
		foreach ($data1y as $i) {
			$sum += $i;
		}
		$title = $year . " // uploads // total: " . $sum;
		if($degree != -1) {
			if($group) {
				$amd = new Application_Model_DegreeGroupMapper();
				$group = $amd->find($degree);
				$title .= "\n group: " . $group->getName();
			} else {
				$amd = new Application_Model_DegreeMapper();
				$degree = $amd->find($degree);
				$title .= "\n degree: " . $degree->getName();
			}
		}
		$graph->title->Set($title);
		
		// Display the graph
		$graph->Stroke();
    
    	exit();
    }
    
    public function graphUploadTotalTypesAction()
    {
    	$stats = new Application_Model_Statistics();
    		
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    
    
    
    
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    
    	$group = false;
    	$degree = -1;
    
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    
    	$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getExamUploadsYearByType($year, $degree, $group);
    
    	
    
    	  
    
    	// Create the graph. These two calls are always required
    	$graph = new Graph(700,300,'auto');
    	$graph->SetScale("textint");
    
    	$theme_class=new UniversalTheme;
    	$graph->SetTheme($theme_class);
    
    	//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
    	$graph->SetBox(false);
    
    	$graph->ygrid->SetFill(false);
    	$graph->xaxis->SetTickLabels($months);
    	$graph->yaxis->HideLine(false);
    	$graph->yaxis->HideTicks(false,false);
    

    	
    	
    	
    	$gbbplot_array = array();
    	$amey = new Application_Model_ExamTypeMapper();
    	$all = $amey->fetchAll();
    	
    	// add all months upload in plain format
    	foreach ($results as $type_id => $type) {
    		$a = array();
    		foreach ($type as $month) {
    			$a[] = $month['uploads'];
    		}
    		$bplot = new BarPlot($a);
    		$bplot->SetLegend($amey->find($type_id)->getName());
    		$gbbplot_array[] = $bplot;
    	}
    	
    		
    	// Create the grouped bar plot
    	$gbbplot = new AccBarPlot($gbbplot_array);
    	$gbplot = new GroupBarPlot(array($gbbplot));
    	
    	
    	$graph->Add($gbplot);
    	
   	
    	$sum = 0;
    	foreach ($results as $typs) {
    		foreach ($typs as $month) {
    			$sum += $month['uploads'];
    		}
    		
    	}
    	$title = $year . " // uploads // total: " . $sum;
    	if($degree != -1) {
    		if($group) {
    			$amd = new Application_Model_DegreeGroupMapper();
    			$group = $amd->find($degree);
    			$title .= "\n group: " . $group->getName();
    		} else {
    			$amd = new Application_Model_DegreeMapper();
    			$degree = $amd->find($degree);
    			$title .= "\n degree: " . $degree->getName();
    		}
    	}
    	$graph->title->Set($title);
    
    	// Display the graph
    	$graph->Stroke();
    
    	exit();
    }
    
    public function graphUploadTotalPieGroupsAction()
    {
    	$stats = new Application_Model_Statistics();
    		
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
		require_once ('jpgraph/jpgraph_pie.php');
		
		$request = $this->getRequest ();
		if (isset ( $request->year )) {
			$year = $request->year;
		} else {
			$year = date("Y");
		}
	
		$data1y = $stats->getExamAllGroupsUploads($year);
    
    	$data = $data1y;
    	
    	// Create the Pie Graph.
    	$graph = new PieGraph(350,380);
    	
    	$theme_class="DefaultTheme";
    	//$graph->SetTheme(new $theme_class());
    	
    	// Set A title for the plot
    	$graph->title->Set("Distribution over degree groups");
    	$graph->SetBox(true);
    	
    	// Create
    	$p1 = new PiePlot($data);
    	$graph->Add($p1);
    	
    	$axis = array();
    	foreach ($stats->getAllDegreeGroups() as $elemet) {
    		$axis[] = $elemet['name'];
    	}
    	$p1->SetLegends($axis);
    	//$graph->legend->SetMargin(20,5);
    	
    	

		$graph->legend->SetPos(0.5,0.97,'center','bottom');
		$graph->legend->SetColumns(1);
    	
    	//$graph->SetLegends();
    	
    	$p1->ShowBorder();
    	$p1->SetColor('black');
    	//$p1->SetSliceColors(array('#1E90FF','#2E8B57','#ADFF2F','#DC143C','#BA55D3'));
    	$graph->Stroke();
    
    
    	
    
    	exit();
    }
    
    public function graphUploadTotalPieDegreesAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_pie.php');
    
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    
    	// Some data and the labels
    	$data   = $stats->getExamAllDegreesUploads($year);
    	$labels = $stats->getAllDegrees();
    	
    	$new_data = array();
    	foreach ($data as $i => $dat) {
    		if($dat != 0) {
    			$new_data[] = array('data' => $dat, 'lable' => $labels[$i]['name'] );
    			
    		}
    	}
    	
    	
    	$labels = array();
    	foreach ($new_data as $lable) {
    		$labels[] = substr($lable['lable'], 0, 30)." (%.1f%%)";
    	}
    	
    	$data = array();
    	foreach ($new_data as $dat) {
    		$data[] = $dat['data'];
    	}

    	
    	// Create the Pie Graph.
    	$graph = new PieGraph(700,380, "auto");
    	$graph->SetShadow();
    	
    	// Set A title for the plot
    	$graph->title->Set('Distribution over degrees');
    	$graph->title->SetColor('black');
    	
    	// Create pie plot
    	$p1 = new PiePlot($data);
    	$p1->SetCenter(0.5,0.5);
    	$p1->SetSize(0.3);
    	
    	// Enable and set policy for guide-lines. Make labels line up vertically
    	$p1->SetGuideLines(true,false);
    	$p1->SetGuideLinesAdjust(1.1);
    	
    	// Setup the labels to be displayed
    	$p1->SetLabels($labels);
    	
    	// This method adjust the position of the labels. This is given as fractions
    	// of the radius of the Pie. A value < 1 will put the center of the label
    	// inside the Pie and a value >= 1 will pout the center of the label outside the
    	// Pie. By default the label is positioned at 0.5, in the middle of each slice.
    	$p1->SetLabelPos(1);
    	
    	// Setup the label formats and what value we want to be shown (The absolute)
    	// or the percentage.
    	$p1->SetLabelType(PIE_VALUE_PER);
    	$p1->value->Show();
    	$p1->value->SetColor('black');
    	
    	// Add and stroke
    	$graph->Add($p1);
    	$graph->Stroke();
    
    
    	exit();
    }
    
    public function downloadAction()
    {
    	$stats = new Application_Model_Statistics();
    	 
    	// define years
    	$this->view->upload_years = $stats->getEaxamDownlaodsAllUsedYears();
    
    	 
    	$this->view->upload_degrees = $stats->getAllDegrees();
    	 
    	$this->view->upload_groups = $stats->getAllDegreeGroups();
    	
    	 
    	 
    }
    
    
    public function downloadCourseAction()
    {
    	$stats = new Application_Model_Statistics();
    
    	// define years
    	$this->view->upload_years = $stats->getEaxamDownlaodsAllUsedYears(); 

    	$request = $this->getRequest ();
    	if (isset( $request->course )) {
    		$course=$request->course;
    	} else {
    		throw new Exception("no course id given");
    	}
    	
    	$year = 'y'.date("Y");
    	
    	if (isset( $request->year )) {
    		$year=$request->year;
    	} else {
    		if(count($this->view->upload_years) >= 1) {
    			$year = 'y'.$this->view->upload_years[0];
    		}
    	}
    	
    	$this->view->year = $year;
    	
    	
    	$this->view->course = $course;
    
    
    }
    
    public function downloadExamAction()
    {
    	$stats = new Application_Model_Statistics();
    
    	// define years
    	$this->view->upload_years = $stats->getEaxamDownlaodsAllUsedYears();
    
    	$request = $this->getRequest ();
    	if (isset( $request->exam )) {
    		$exam=$request->exam;
    	} else {
    		throw new Exception("no exam id given");
    	}
    	
    	$year = 'y'.date("Y");
    	 
    	if (isset( $request->year )) {
    		$year=$request->year;
    	} else {
    		if(count($this->view->upload_years) >= 1) {
    			$year = 'y'.$this->view->upload_years[0];
    		}
    	}
    	 
    	$this->view->year = $year;
    	 
    	$this->view->exam = $exam;
    
    
    }
    
    
    public function graphDownloadCourseAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_scatter.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	require_once ('jpgraph/jpgraph_plotline.php');
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		throw new Exception();
    	}
    	
    	$course = -1;
    	
    	if (isset ( $request->course )) {
    		$course=$request->course;
    	} else {
    		throw new Exception("no course id given");
    	}
    	
    	//$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getCourseDownloadsDailyYear($year, $course);
    	
    	//var_dump($results);
    	//die();
    	
    	$days = array();
    	$downloads = array();
    	$total_downloads = 0;
    	foreach ($results as $day=>$download)
    	{
    		//echo($day);
    		$days[] = $day;
    		$downloads[] = $download;
    		$total_downloads += $download;
    	}
    	//die();
    	
    	$datay = $downloads;//array(3.5,3.7,3,4,6.2,6,3.5,8,14,8,11.1,13.7);
    	$datax = $days;//array(20,22,12,13,17,20,16,19,30,31,40,43);
    	$graph = new Graph(900,380);
    	$graph->img->SetMargin(40,40,40,40);
    	$graph->img->SetAntiAliasing();
    	$graph->SetScale("textlin");
    	$graph->SetShadow();
    	
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));

    	$markings = array();
    	for ($i = 0; $i < 365; $i++) {
    		if($i%15 == 0 and $i%30 != 0) {
    			$markings[] = $i;
    		}
    	}
    	
    	$months = $gDateLocale->GetShortMonth();
    	
    	$graph->xaxis->SetTickPositions($markings, NULL, $months);
    	
    	
    	/*$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    	 
    	$lplot = new LinePlot($data6y);
    	 
    	$graph->Add($lplot);*/
   	
    	
    	$title = ($year . " // Downloads // total: ". $total_downloads);
    	
    	
    	$amc = new Application_Model_CourseMapper();
    	$cor = $amc->find($course);
    	
    	$title .= "\nfor: " . $cor->getName();
    
    	
    	$graph->title->Set($title);
    	//$graph->title->SetFont(FF_FONT1,FS_BOLD);
    	
    	$band = new PlotBand(VERTICAL,BAND_RDIAG,"min","max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);
    	
    	
    	$b1plot = new BarPlot($datay);
    	$gbplot = new GroupBarPlot(array($b1plot));
    	$graph->Add($gbplot);
    	
    	
    	$colorArray = array('orangered', 'darkolivegreen3', 'deepskyblue3', 'yellow2', 'purple2', 'lightpink2', 'goldenrod1');
    	
    	
    	$exami = $stats->getExamExamination($course, $year);
    	
    	for ($i = 0; $i < count($exami); $i++) {
    		$plotline = new PlotLine(VERTICAL,$exami[$i]['days']+0.5,$colorArray[$i]);
    		$plotline->SetLineStyle('dashed');
    		$plotline->SetLegend($exami[$i]['comment']);
    		$graph->AddLine($plotline);   		
    	}


    	for ($i = 0; $i < 52; $i++) {
    		$plotline = new PlotLine(VERTICAL,($i * 7) -0.5, 'gray8');
    		$graph->AddLine($plotline);
    	}
    	
    	$graph->Stroke();
    	
    	exit();
    
    }
    
    
    public function graphDownloadExamAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_scatter.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	require_once ('jpgraph/jpgraph_plotline.php');
    	 
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		throw new Exception();
    	}
    	 
    	$exam = -1;
    	 
    	if (isset ( $request->exam )) {
    		$exam=$request->exam;
    	} else {
    		throw new Exception("no course id given");
    	}
    	 
    	//$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getExamDownloadsDailyYear2($year, $exam);

    	$days = array();
    	$downloads = array();
    	$total_downloads = 0;
    	foreach ($results as $day=>$download)
    	{
    		//echo($day);
    		$days[] = $day;
    		$downloads[] = $download;
    		$total_downloads += $download;
    	}
    	//die();
    	 
    	$datay = $downloads;//array(3.5,3.7,3,4,6.2,6,3.5,8,14,8,11.1,13.7);
    	$datax = $days;//array(20,22,12,13,17,20,16,19,30,31,40,43);
    	$graph = new Graph(900,380);
    	$graph->img->SetMargin(40,40,40,40);
    	$graph->img->SetAntiAliasing();
    	$graph->SetScale("textlin");
    	$graph->SetShadow();
    	 
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));
    
    	$markings = array();
    	for ($i = 0; $i < 365; $i++) {
    		if($i%15 == 0 and $i%30 != 0) {
    			$markings[] = $i;
    		}
    	}
    	 
    	$months = $gDateLocale->GetShortMonth();
    	 
    	$graph->xaxis->SetTickPositions($markings, NULL, $months);
    	 
    	 
    	/*$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    
    	$lplot = new LinePlot($data6y);
    
    	$graph->Add($lplot);*/
    
    	 
    	$title = ($year . " // Downloads // total: ". $total_downloads);
    	 
    	 
    	$amc = new Application_Model_ExamMapper();
    	$cor = $amc->find($exam);
    	 
    	$title .= "\nfor exam id: " . $cor->getId();
    
    	 
    	$graph->title->Set($title);
    	//$graph->title->SetFont(FF_FONT1,FS_BOLD);
    	 
    	$band = new PlotBand(VERTICAL,BAND_RDIAG,"min","max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);
    	 
    	 
    	$b1plot = new BarPlot($datay);
    	$gbplot = new GroupBarPlot(array($b1plot));
    	$graph->Add($gbplot);
    	 
    	 
    	$colorArray = array('orangered', 'darkolivegreen3', 'deepskyblue3', 'yellow2', 'purple2', 'lightpink2', 'goldenrod1');
    	 
    	 
    	$exami = $stats->getExamExaminationByExam($exam, $year);
    	 
    	for ($i = 0; $i < count($exami); $i++) {
    		$plotline = new PlotLine(VERTICAL,$exami[$i]['days']+0.5,$colorArray[$i]);
    		$plotline->SetLineStyle('dashed');
    		$plotline->SetLegend($exami[$i]['comment']);
    		$graph->AddLine($plotline);
    	}
    
    
    	for ($i = 0; $i < 52; $i++) {
    		$plotline = new PlotLine(VERTICAL,($i * 7) -0.5, 'gray8');
    		$graph->AddLine($plotline);
    	}
    	 
    	$graph->Stroke();
    	 
    	exit();
    
    }
    
    public function ajaxDownloadRankingAction()
    {
    	
    	//if(!$this->_authManager->isAllowed(null, 'modify_course'))
    	//	throw new Custom_Exception_PermissionDenied("Permission Denied");
    	
    	$stats = new Application_Model_Statistics();
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	 
    	$group = false;
    	$degree = -1;
    	 
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	 
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	$page = 1;
    	$max_elements = 30;
    	if (isset ( $request->elements )) {
    		$max_elements=$request->elements;
    	}
    	
    	if (isset ( $request->page )) {
    		$page = $request->page;
    	}
    	
    
    	$results = $stats->getExamDownloadsRankingYear($year, $degree, $group);
    	

    	$results2 = array_slice($results, ($page-1) * $max_elements, $max_elements);
    	
    	//var_dump($results2);
    	//die();
    	
    	if(count($results2) == 0) {
    		$this->_helper->json(array());
    		exit();
    	}
    	
    	$em = new Application_Model_ExamMapper();
    	$exams = array();
    	
    	//var_dump($em->find(1337));
    	$ids = array();
    	
    	foreach ($results2 as $exam) {
    		$ids[] = $exam['idexam'];
    		
    	}
    	
    	//var_dump($ids);
    	//die();
    	
    	
    	$res = $em->fetchQuick(-1, -1, -1, -1, -1, array(), true, $ids);
    	
    	//var_dump($res);
    		
    	foreach ($res as $ex) {
    		$cors = array();
    		foreach ($ex->getCourse() as $cor) {
    			$cors[] = array('name' => $cor->getName(), 'id' => $cor->getId() );
    		}
    		$ccors = array();
    		foreach ($ex->getCourseConnected() as $cor) {
    			$ccors[] = $cor->getName();
    		}
    		$lect = array();
    		foreach ($ex->getLecturer() as $cor) {
    			$lect[] = $cor->getName() . ", " . $cor->getDegree() . " " . $cor->getFirstName();
    		}
    		$files = array();
    		foreach ($ex->getDocuments() as $cor) {
    			//$files[] = array('name' => $cor->getDisplayName().".".$cor->getExtention(), 'id'=>$cor->getId());
    			$files[] = array('name' => ".".$cor->getExtention(), 'id'=>$cor->getId());
    			 
    		}
    		
    		$rank = -1;
    		$downlow = -1;
    		foreach ($results2 as $element) {
    			if($element['idexam'] == $ex->getId()) {
    				$rank = $element['rank'];
    				$downlow = $element['downloads'];
    			}
    		}
    		$exams[] = array(
    				'idexam' =>$ex->getId(),
    				'downloads' => $downlow,
    				'rank' => $rank,
    				'comment' =>$ex->getComment(),
    				'course' => $cors,
    				'course_connected' => $ccors,
    				'degree'=> $ex->getDegree()->getName(),	
    				'semester'=> $ex->getSemester()->getName(),
    				'lecturer'=> $lect,
    				'type'=> $ex->getType()->getName(),
    				'sub_type'=> $ex->getSubType()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'autor'=> $ex->getAutor(),
    				'files' => $files,
    				'uni' => $ex->getUniversity()->getName(),
    		);
    	}
    	
    	// define a custom month sort
    	function cmp($a, $b)
    	{
    		if ($a['rank'] == $b['rank']) {
    			return 0;
    		}
    		return ($a['rank'] < $b['rank']) ? -1 : 1;
    	}
    	
    	usort($exams, "cmp");
    	
    	   	
    	$this->_helper->json($exams);
    	
    	exit();
    	 
    
    
    }
    
    public function ajaxDownloadRankingCourseAction()
    {
   	 
    	$stats = new Application_Model_Statistics();
    	 
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    
    	$group = false;
    	$degree = -1;
    
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	 
    	$page = 1;
    	$max_elements = 30;
    	if (isset ( $request->elements )) {
    		$max_elements=$request->elements;
    	}
    	 
    	if (isset ( $request->page )) {
    		$page = $request->page;
    	}
    	 
    
    	$results = $stats->getCoursDownloadsRankingGroupsYear($year, $degree, $group);
    	 
    
    	$results2 = array_slice($results, ($page-1) * $max_elements, $max_elements);

    	
    	$cours = array();
    	$rank = 1;
    	foreach ($results2 as $ex) {    
    		
    		
    		$cours[] = array(
    				'idcourse' => $ex['idcourse'],
    				'downloads' => $ex['downloads'],
    				'name' => $ex['cours_name'],
    				'rank' => $rank,
    		);
    		
    		$rank++;
    		
    		
    	}
    	 
    	// define a custom month sort
    	function cmp($a, $b)
    	{
    		if ($a['rank'] == $b['rank']) {
    			return 0;
    		}
    		return ($a['rank'] < $b['rank']) ? -1 : 1;
    	}
    	 
    	usort($cours, "cmp");
    	 
    		
    	
    	$this->_helper->json($cours);
    	
    	exit();
    
    
    }
    
    public function ajaxDownloadRankingCourse2Action()
    {
    	 
    	//if(!$this->_authManager->isAllowed(null, 'modify_course'))
    	//	throw new Custom_Exception_PermissionDenied("Permission Denied");
    	 
    	$stats = new Application_Model_Statistics();
    	 
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	
    	$course = -1;
    
    	if (isset ( $request->course )) {
    		$course=$request->course;
    	}
    
    	    	 
    	$page = 1;
    	$max_elements = 30;
    	if (isset ( $request->elements )) {
    		$max_elements=$request->elements;
    	}
    	 
    	if (isset ( $request->page )) {
    		$page = $request->page;
    	}
    	 
    
    	$results = $stats->getCoursDownloadsRankingYear($year, $course);
    	 
    
    	$results2 = array_slice($results, ($page-1) * $max_elements, $max_elements);
    	 
    	//var_dump(count($results));
    	//die();
    	
    	if(count($results) == 0) {
    		$this->_helper->json(array());
    		exit();
    	}
    	 
    	$em = new Application_Model_ExamMapper();
    	$exams = array();
    	 
    	//var_dump($em->find(1337));
    	$ids = array();
    	 
    	foreach ($results2 as $exam) {
    		$ids[] = $exam['idexam'];
    
    	}
    	 
    	//var_dump($ids);
    	//die();
    	 
    	 
    	$res = $em->fetchQuick(-1, -1, -1, -1, -1, array(), true, $ids);
    	 
    	//var_dump($res);
    
    	foreach ($res as $ex) {
    		$cors = array();
    		foreach ($ex->getCourse() as $cor) {
    			$cors[] = array('name' => $cor->getName(), 'id' => $cor->getId() );
    		}
    		$ccors = array();
    		foreach ($ex->getCourseConnected() as $cor) {
    			$ccors[] = $cor->getName();
    		}
    		$lect = array();
    		foreach ($ex->getLecturer() as $cor) {
    			$lect[] = $cor->getName() . ", " . $cor->getDegree() . " " . $cor->getFirstName();
    		}
    		$files = array();
    		foreach ($ex->getDocuments() as $cor) {
    			//$files[] = array('name' => $cor->getDisplayName().".".$cor->getExtention(), 'id'=>$cor->getId());
    			$files[] = array('name' => ".".$cor->getExtention(), 'id'=>$cor->getId());
    
    		}
    
    		$rank = -1;
    		$downlow = -1;
    		foreach ($results2 as $element) {
    			if($element['idexam'] == $ex->getId()) {
    				$rank = $element['rank'];
    				$downlow = $element['downloads'];
    			}
    		}
    		$exams[] = array(
    				'idexam' =>$ex->getId(),
    				'downloads' => $downlow,
    				'rank' => $rank,
    				'comment' =>$ex->getComment(),
    				'course' => $cors,
    				'course_connected' => $ccors,
    				'degree'=> $ex->getDegree()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'lecturer'=> $lect,
    				'type'=> $ex->getType()->getName(),
    				'sub_type'=> $ex->getSubType()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'autor'=> $ex->getAutor(),
    				'files' => $files,
    				'uni' => $ex->getUniversity()->getName(),
    		);
    	}
    	 
    	// define a custom month sort
    	function cmp($a, $b)
    	{
    		if ($a['rank'] == $b['rank']) {
    			return 0;
    		}
    		return ($a['rank'] < $b['rank']) ? -1 : 1;
    	}
    	 
    	usort($exams, "cmp");
    	 
    		
    	$this->_helper->json($exams);
    	 
    	exit();
    
    
    
    }
    
    public function ajaxDownloadRankingSingleExamAction()
    {
    
    	//if(!$this->_authManager->isAllowed(null, 'modify_course'))
    	//	throw new Custom_Exception_PermissionDenied("Permission Denied");
    
    	$stats = new Application_Model_Statistics();
    
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	 
    	$exam = -1;
    
    	if (isset ( $request->exam )) {
    		$exam=$request->exam;
    	}
    
    
    	 
    	if($exam == -1) {
    		$this->_helper->json(array());
    		exit();
    	}
    
    	$em = new Application_Model_ExamMapper();
    	$exams = array();
    
    	$res = $em->find($exam);
    	
    	
    	$results = $stats->getCoursDownloadsRankingYear($year, $res->getCourse()[0]->getId());
    	

    
    	foreach (array($res) as $ex) {
    		$cors = array();
    		foreach ($ex->getCourse() as $cor) {
    			$cors[] = array('name' => $cor->getName(), 'id' => $cor->getId() );
    		}
    		$ccors = array();
    		foreach ($ex->getCourseConnected() as $cor) {
    			$ccors[] = $cor->getName();
    		}
    		$lect = array();
    		foreach ($ex->getLecturer() as $cor) {
    			$lect[] = $cor->getName() . ", " . $cor->getDegree() . " " . $cor->getFirstName();
    		}
    		$files = array();
    		foreach ($ex->getDocuments() as $cor) {
    			//$files[] = array('name' => $cor->getDisplayName().".".$cor->getExtention(), 'id'=>$cor->getId());
    			$files[] = array('name' => ".".$cor->getExtention(), 'id'=>$cor->getId());
    
    		}
    
    		$rank = -1;
    		$downlow = -1;
    		foreach ($results as $element) {
    			if($element['idexam'] == $ex->getId()) {
    				$rank = $element['rank'];
    				$downlow = $element['downloads'];
    			}
    		}
    		$exams[] = array(
    				'idexam' =>$ex->getId(),
    				'downloads' => $downlow,
    				'rank' => $rank,
    				'comment' =>$ex->getComment(),
    				'course' => $cors,
    				'course_connected' => $ccors,
    				'degree'=> $ex->getDegree()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'lecturer'=> $lect,
    				'type'=> $ex->getType()->getName(),
    				'sub_type'=> $ex->getSubType()->getName(),
    				'semester'=> $ex->getSemester()->getName(),
    				'autor'=> $ex->getAutor(),
    				'files' => $files,
    				'uni' => $ex->getUniversity()->getName(),
    		);
    	}
    
    	// define a custom month sort
    	function cmp($a, $b)
    	{
    		if ($a['rank'] == $b['rank']) {
    			return 0;
    		}
    		return ($a['rank'] < $b['rank']) ? -1 : 1;
    	}
    
    	usort($exams, "cmp");
    
    
    	$this->_helper->json($exams);
    
    	exit();
    
    
    
    }
    
    public function graphDownloadAction()
    {
    	$stats = new Application_Model_Statistics();
    
    
    	$path = '../library/jpgraph';
    	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    	require_once ('jpgraph/jpgraph.php');
    	require_once ('jpgraph/jpgraph_scatter.php');
    	require_once ('jpgraph/jpgraph_bar.php');
    	require_once ('jpgraph/jpgraph_line.php');
    	require_once ('jpgraph/jpgraph_plotline.php');
    	 
    	
    	$request = $this->getRequest ();
    	if (isset ( $request->year )) {
    		$year = $request->year;
    	} else {
    		$year = date("Y");
    	}
    	
    	$group = false;
    	$degree = -1;
    	
    	if (isset ( $request->group )) {
    		$degree=$request->group;
    		$group = true;
    	}
    	
    	if (isset ( $request->degree )) {
    		$degree = $request->degree;
    	}
    	
    	//$months = $gDateLocale->GetShortMonth();
    	$results = $stats->getExamDownloadsDailyYear($year, $degree, $group);
    	
    	//var_dump($results);
    	//die();
    	
    	$days = array();
    	$downloads = array();
    	$total_downloads = 0;
    	foreach ($results as $day=>$download)
    	{
    		//echo($day);
    		$days[] = $day;
    		$downloads[] = $download;
    		$total_downloads += $download;
    	}
    	//die();
    	
    	$datay = $downloads;//array(3.5,3.7,3,4,6.2,6,3.5,8,14,8,11.1,13.7);
    	$datax = $days;//array(20,22,12,13,17,20,16,19,30,31,40,43);
    	$graph = new Graph(900,380);
    	$graph->img->SetMargin(40,40,40,40);
    	$graph->img->SetAntiAliasing();
    	$graph->SetScale("textlin");
    	$graph->SetShadow();
    	
    	//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
    	//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));

    	$markings = array();
    	for ($i = 0; $i < 365; $i++) {
    		if($i%15 == 0 and $i%30 != 0) {
    			$markings[] = $i;
    		}
    	}
    	
    	$months = $gDateLocale->GetShortMonth();
    	
    	$graph->xaxis->SetTickPositions($markings, NULL, $months);
    	
    	
    	/*$data6y=array(50,58,60,58,53,58,57,60,58,58,57,50);
    	 
    	$lplot = new LinePlot($data6y);
    	 
    	$graph->Add($lplot);*/
   	
    	
    	$title = ($year . " // Downloads // total: ". $total_downloads);
    	if($degree != -1) {
    		if($group) {
    			$amd = new Application_Model_DegreeGroupMapper();
    			$group = $amd->find($degree);
    			$title .= "\n group: " . $group->getName();
    		} else {
    			$amd = new Application_Model_DegreeMapper();
    			$degree = $amd->find($degree);
    			$title .= "\n degree: " . $degree->getName();
    		}
    	}
    	
    	$graph->title->Set($title);
    	//$graph->title->SetFont(FF_FONT1,FS_BOLD);
    	
    	$band = new PlotBand(VERTICAL,BAND_RDIAG,"min","max",'khaki4');
    	$band->ShowFrame(true);
    	$band->SetOrder(DEPTH_BACK);
    	$graph->Add($band);
    	
    	
    	$b1plot = new BarPlot($datay);
    	$gbplot = new GroupBarPlot(array($b1plot));
    	
    	/*$sp1 = new ScatterPlot($datay,);
    	$sp1->SetLinkPoints(true,"red",2);
    	$sp1->mark->SetType(MARK_FILLEDCIRCLE);
    	$sp1->mark->SetFillColor("navy");
    	$sp1->mark->SetWidth(0);*/
    	for ($i = 0; $i < 52; $i++) {
    		$plotline = new PlotLine(VERTICAL,($i * 7) -0.5, 'gray8');
    		$graph->AddLine($plotline);
    	}
    	
    	
    	$graph->Add($gbplot);
    	$graph->Stroke();
    	
    	exit();
    }

}

