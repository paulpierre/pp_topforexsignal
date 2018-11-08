 $(document).ready(function() {
		$(".editDelete").show();
		$(".editLink").each(function(index,value){
			$('.editLink')[index].dispatchEvent(new MouseEvent("click"));
		});
		window.scrollTo(0,1200);
		console.log("DONE!");
		
		$("#saveListSettings").click(function(){
			var a = setTimeout(function(){
				window.location='https://www.aweber.com/users/broadcasts';
			},2000);
		});
});
