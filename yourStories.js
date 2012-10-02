function likeOthers(){
	
}
function getOthersPosts(){
	user = window.user_credentials.authResponse;
		$.post('likeOthers.php',
			{ 'getOthersPosts': '1' },
			function(data){
				//alert(data);
				
			});
}
