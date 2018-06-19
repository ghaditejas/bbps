$(document).ready(function() {
	
	
	// $('input[type=file]').bootstrapFileInput();
	// $('.file-inputs').bootstrapFileInput();
	$('#utSelect').prop('selectedIndex', -1);
	$('#utSelect2').prop('selectedIndex', -1);

	$('.opcbox').off('click').on('click', function (event) {
		$('.thlist').fadeOut(200);
		setTimeout(function () {
			$('.opcrow').fadeIn(300);
			$('.closetab').fadeIn(300);
		}, 100);
	});	
	
    $('.closetab').off('click').on('click', function () {
		$('.closetab').fadeOut(200);				
		$('.opcrow').fadeOut(200);
		setTimeout(function () {
			$('.thlist').fadeIn(300);
		}, 100);
    });
	
	$('.total-countbox').off('click').on('click', function (event) {		
		setTimeout(function () {
			$('.totaltrans').fadeIn(300);			
		}, 100);
	});
	
	$('.fliterbtn').off('click').on('click', function (event) {
		//$('.fliterbox').fadeOut(200);
		setTimeout(function () {
			$('.remover-table').fadeIn(300);
		}, 100);
	});	
	

}); // (jQuery)End of use strict
