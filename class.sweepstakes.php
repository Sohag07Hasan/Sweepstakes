<?php
/*
 * plugin name: Sweepstakes management for wp sites
 * */
 

 
class sweepstakes_management{
	static function init(){
		add_action('admin_menu', array(get_class(), 'admin_menu'));
		add_action('wp_enqueue_scripts', array(get_class(), 'enqueue_scripts'));
		add_shortcode('wp_sweepstakes', array(get_class(), 'shotcode_replacement'));
		add_action('wp_footer', array(get_class(), 'wp_footer') );
	}
	
	/*
	 * settigs page
	 */
	static function admin_menu(){
		add_options_page('sweepstaker setting page', 'Sweepstakes', 'manage_options', 'sweep_options_page', array(get_class(), 'options_page_content'));
	}
	
	/*
	 * option page content
	 * */
	static function options_page_content(){
		if($_POST['sweep-submit'] == 'Y') :
			$data = array(
				'intro' => stripslashes($_POST['sweep-intro']),
				'terms' => $_POST['sweep-terms'],
				'title' => $_POST['sweep-reg-title'],
				'confirm' => $_POST['sweep-confirm'],
				'start' => $_POST['sweep-start'],
				'end' => $_POST['sweep-end']
			);
			update_option('wp_sweepstakes', $data);			
		endif;
		
		$info = self::get_sweep_info();
		include dirname(__FILE__) . '/includes/options-page.php';
	}
	
	static function get_sweep_info(){
		return get_option('wp_sweepstakes');
	}
	
	
	//adding js an css
	static function enqueue_scripts(){
		wp_enqueue_script('jquery');
		wp_register_script('jquery-ui-dialogue_js', plugins_url('', __FILE__) . '/dialogue-jquery-ui/js/jquery-ui-1.8.20.custom.min.js');
		wp_enqueue_script('jquery-ui-dialogue_js');
		
		wp_register_style('jquery-ui-dialogue_css', plugins_url('', __FILE__) . '/dialogue-jquery-ui/css/ui-lightness/jquery-ui-1.8.20.custom.css');
		wp_enqueue_style('jquery-ui-dialogue_css');
		
		wp_register_style('sweepstakes-default-css', plugins_url('', __FILE__) . '/css/style.css');
		wp_enqueue_style('sweepstakes-default-css');
	}
	
	//replace the shotcode
	static function shotcode_replacement(){
		//if($_POST['accept-the-term'] == 'Y') :
			$content = "<div style='border: 1px solid #1E90FF;' class='sweepstakes-signup-holder'>";
			$content .= '<h2 class="text-center">Sweepstakes Lucky Draw Entry Form</h2>';
			$content .= self::get_signup_form();
			$content .= "</div>";
			return $content;
			
		//else:
			$content = "<div class='sweepstakes-content-termpage'>";
			$content .= '<h2 style="text-align: center; margin-top: 25px;">To Enter the Sweepstakes:</h2>';
			$content .= '<p style="text-align: center;">';
			$content .= 'View the <a href="#" id="dialog_opener" class="ui-state-default ui-corner-all">terms and conditions here</a> ';
			$content .= 'Once you agree to these terms, please proceed to the next page to enter your code for the sweepstakes drawing.';
			$content .= '</p>';
			$content .= self::get_accept_form();
			$content .= "</div>";
			$content .= self::get_terms_conditions();
		//endif;
		return $content;
	}
	
	static function dialogue_js($temrs){
		$temrs .= "<script type='text/javascript'>
			jQuery()
		</script>";
	}
	
	//temrs content
	static function get_terms_conditions(){
		$info = self::get_sweep_info();
		$terms = "<div id='sweepstakes-terms-conditions' title='Terms and Conditions'>" . $info['terms'] . "</div>";
		return $terms;
	}
	
	//adding js in footer
	static function wp_footer(){
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$( "#sweepstakes-terms-conditions" ).dialog({
					autoOpen: false,
					width: 700,
					height:500,
					show: "blind",
					hide: "explode"
				});

				$( "#dialog_opener" ).click(function() {
					$( "#sweepstakes-terms-conditions" ).dialog( "open" );
					return false;
				});
				
				//hover states on the static widgets
				$('#dialog_opener, ul#icons li').hover(
					function() { $(this).addClass('ui-state-hover'); },
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
			});
		</script>
		<?php
	}
	
	static function get_accept_form(){
		$form = "<form action='' method='post'>";
		$form .= "<p style='text-align:center'><input type='checkbox' name='accept-the-term' value='Y' /> I agree ";
		$form .= "<input type='submit' value='proceed' /></p>";
		$form .= "</form>";
		return $form;
	}
	
	static function get_signup_form(){
		$form = "<form action='' method='post'>
			<input type='hidden' name='signed-up' value='Y' />
			<table class='signup-table'>
				<tr>
					<td>Name:</td> <td><input style='width:60%' type='text' name='sw-name'></td>
				</tr>
				<tr>
					<td>Email:</td> <td><input style='width:60%' type='text' name='sw-email'></td>
				</tr>
				<tr>
					<td>Code(First 5): </td> <td><input style='width:60%' type='text' name='sw-code-first'></td>
				</tr>
				<tr>
					<td>Code(Last 5):</td> <td><input style='width:60%' type='text' name='sw-code-last'></td>
				</tr>
				<tr>
					<td colspan='2'><code>Please enter the code you received in your giveaway. Do not use dashes or spaces. </code>	</td>
				</tr>
				
				<tr>
					<td><input type='submit' name='SignUp' value= 'SignUp'/></td>
				</tr>
				
			</table>
					
		</form>";
		return $form;
	}
	
}


//initializing everything
sweepstakes_management :: init();

