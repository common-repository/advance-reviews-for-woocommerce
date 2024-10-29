jQuery(document).ready(function($){
	
	if(plugin_enable==1){
		jQuery('#submit').click(function(event){
			
			var coment=jQuery('#comment').val();
			var coment_title=jQuery('#title').val();
			
			if(coment==''){
				event.preventDefault();
				alert('Comment Field is Empty');
			}
			if(coment_title==''){
				event.preventDefault();
				alert('Title is Empty');
			}
			
			
		});
	}
		
});