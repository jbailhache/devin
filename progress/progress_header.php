<?php
if (file_exists(__DIR__ . '/config.php')) {
	include_once(__DIR__ . '/config.php');
}
if (!defined('PROGRESS_BAR_ENABLED')) {
	define('PROGRESS_BAR_ENABLED', true);
}
// Apache + php-fpm (mod_proxy_fcgi) garde en tampon les petits envois : chaque mise à jour est complétée à cette taille
if (!defined('PROGRESS_FLUSH_PADDING')) {
	define('PROGRESS_FLUSH_PADDING', 4096);
}
if (PROGRESS_BAR_ENABLED) {
	// Désactive toute mise en tampon pour que chaque mise à jour parte immédiatement vers le navigateur
	@ini_set('zlib.output_compression', '0');
	@ini_set('output_buffering', '0');
	@ini_set('implicit_flush', '1');
	if (function_exists('apache_setenv')) {
		@apache_setenv('no-gzip', '1');
	}
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=utf-8');
		header('Cache-Control: no-cache');
		header('X-Accel-Buffering: no');
	}
	while (ob_get_level() > 0) {
		ob_end_flush();
	}
	ob_implicit_flush(true);
}
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
	$progress_step = max(1, intval($progress_max/100));
	echo "<div class=\"progress-data-console\"><script type=\"text/javascript\">progBar.progressbar({max : $progress_max}).trigger('progressbarchange');</script>";
	progressFlush();
}

/**
 * Envoie immédiatement au navigateur ce qui a été produit
 */
function progressFlush(){
	echo str_repeat(' ', PROGRESS_FLUSH_PADDING);
	if (ob_get_level() > 0) {
		@ob_flush();
	}
	flush();
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
function incrProgressBar($val=0){
	if(!PROGRESS_BAR_ENABLED){
		return;
	}
	global $progress_step, $progress_max;
	if( $val % $progress_step != 0 && $val < $progress_max){
		return;
	}
	echo "<script>progBar.progressbar('value', ".intval($val).");</script>\n";
	progressFlush();
}
?>
