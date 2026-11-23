function GiveHex(dec) {
	return (parseInt(dec).toString(16));
}

/**
 * Convert RGB to HEX
 * 
 * @param Red
 *            red value
 * @param Green
 *            green value
 * @param Blue
 *            blue value
 * @returns
 */
function RGBToHex(Red, Green, Blue) {
	var a = GiveHex(Math.floor(Red / 16));
	var b = GiveHex(Red % 16);
	var c = GiveHex(Math.floor(Green / 16));
	var d = GiveHex(Green % 16);
	var e = GiveHex(Math.floor(Blue / 16));
	var f = GiveHex(Blue % 16);
	var z = a + b + c + d + e + f;
	return z;
}
/**
 * Calculate the progress color
 * 
 * @param x
 *            percentage
 * @returns {String}
 */
function getProgressColor(x) {
	return '#'
			+ RGBToHex(Math.floor(0x00FF * ((100 - x) / 100)), Math
					.floor(0xFF * (x / 100)), 0);
}

function progressDataConsoleGoToBottom(){
	$('.progress-data-console').scrollTop($('.progress-data-console')[0].scrollHeight);
}

$('#progressbar').bind('progressbarchange', function(event, ui) {
   // var selector = "#" + this.id + ' > div';
    var selector = ".ui-progressbar-value";
    
    var valuenow = this.getAttribute( 'aria-valuenow' );
    var valuemax = this.getAttribute( 'aria-valuemax' );

    var pers = Math.round((valuenow/valuemax)*100);
    
    progressLabel = $('.progress-label');
    progressLabel.text(pers+' %');
    progressLabel.css({'font-size':25});
    
    //$(selector).css({'background-color':  getProgressColor(pers), 'background-image': url("/images/progressbar.gif") }); 
    
    $(selector).css({'background':  getProgressColor(pers)});
    
    progressDataConsoleGoToBottom();
});

$(document).ready(function() {
    progressDataConsoleGoToBottom()
});



