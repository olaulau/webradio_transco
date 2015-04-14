$( document ).ready(function(){
	$(".alert").delay(3000).fadeOut(2000, function(){
		$(this).remove();
	});
});