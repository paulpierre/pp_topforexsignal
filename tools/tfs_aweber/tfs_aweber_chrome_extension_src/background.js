
chrome.browserAction.onClicked.addListener(function(tab) {
  chrome.tabs.update({
     url: "https://www.aweber.com/users/settings/#personalizeTab",
	},function(e){
	  chrome.tabs.executeScript(null, {file:"jq.js"}, function() {
	        //chrome.tabs.executeScript(null, {file:"show.js"});
	    });
		/**
		var a = setTimeout(
			function(){
				chrome.tabs.executeScript({
		   	 		code:'console.log("Running script!");$("body").css({display:"none"});$.scrollTo(300);$("#personalizeTabButton").click();$(".editLink").click();console.log("DONE!");'			
				});
	  	},3000);
	**/
	});
});
