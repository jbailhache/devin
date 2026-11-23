<?php
include_once('config.php');
if(PROGRESS_BAR_ENABLED){ ?>
<div id="progressbar"><span class="progress-label"></span></div>
<link rel="stylesheet" href="css/jquery/ui/1.12.1/jquery-ui.min.css"></link>
<link rel="stylesheet" href="css/progress.css"></link>
<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="js/jquery/ui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/progress.js"></script>
<script type="text/javascript">var progBar = $('#progressbar');</script>
<?php }?>
<?php

/**
 * Initalisation du ProgressBar
 * @param unknown $max
 */
function initProgressBar($valueMax){
	if(!PROGRESS_BAR_ENABLED){
		return;
	}
	
	//ob_start();
	
	global $progress_step, $progress_max;
	$progress_max = $valueMax;
	$progress_step = intval($progress_max/100);
	echo "<div class=\"progress-data-console\"><script type=\"text/javascript\">progBar.progressbar({max : $progress_max});</script>";
}

/**
 * Initalisation du ProgressBar
 * @param unknown $max
 */
function endProgressBar(){
	if(!PROGRESS_BAR_ENABLED){
		return;
	}
	echo "</div>";
}
 
/**
 * Mise à jour du ProgressBar
 * @param unknown $value
 */
function incrProgressBar(&$val=0){
	if(!PROGRESS_BAR_ENABLED){
		return;
	}
	global $progress_step, $progress_max;
	if( fmod($val++, $progress_step) != 0 && $val < $progress_max){
		return;
	}
	echo "<script>progBar.progressbar('value', ".($val).");</script>";
}
?>
