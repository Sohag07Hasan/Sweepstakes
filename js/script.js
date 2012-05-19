jQuery(document).ready(function($){
	$('#send-code').click(function(){
		var email = $('#email-for-get-code').val();
		$('#show-message').hide();
		$('#ajax-loader').show();
					
		$.ajax({						
			async: false,
			type: 'post',			
			dataType: "html",
			url: SWEEPSTAKES.ajaxurl,
			cache: false,
			timeout: 10000,
			data:{
				'action': 'sweepstakes_ajax_data',
				'email': email,						
			},

			success:function(result){
				$('#show-message').hide();
				if(result == 1){
					var msg = '<p>Invalid Email!</p>';
					$('#show-message').addClass('email-error');
				}
				else if(result == 2){
					var msg = '<p>This Email has already been registered!</p>';
					$('#show-message').addClass('email-error');
				}				
				else if(result == 3){
					var msg = '<p>Lucky Number sent!</p>';
					$('#show-message').addClass('email-success');
				}
				else{
					var msg = '<p>Server Error! Email not sent. Please try again</p>';
					$('#show-message').addClass('email-error');
				}
						
				$('#show-message').html(msg);
				$('#ajax-loader').hide();
				$('#show-message').show();			
			},

			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#footer').html(textStatus);
				alert(textStatus);
				return false;
			}

		});
		
		return false;	
		
	});
});
