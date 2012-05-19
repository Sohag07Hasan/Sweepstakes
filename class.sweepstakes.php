<?php
/*
 * plugin name: Sweepstakes management for wp sites
 * */
 

 
class sweepstakes_management{
	
	static $error = array();
	static $success = array();
	
	static function init(){
		add_action('admin_menu', array(get_class(), 'admin_menu'));
		add_action('wp_enqueue_scripts', array(get_class(), 'enqueue_scripts'));
		add_shortcode('wp_sweepstakes', array(get_class(), 'shotcode_replacement'));
		add_action('wp_footer', array(get_class(), 'wp_footer') );
		
		add_action('wp_ajax_sweepstakes_ajax_data', array(get_class(), 'ajax_email_sending'));
		add_action('wp_ajax_nopriv_sweepstakes_ajax_data', array(get_class(), 'ajax_email_sending'));
		
		register_activation_hook(__FILE__, array(get_class(), 'table_create'));
		
		add_action('init', array(get_class(), 'handle_signup'));
		
		add_action('wp_footer', array(get_class(), 'PopupHTML'), 100);
		
		
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
		
		wp_register_script('sweepstakes-default-js', plugins_url('', __FILE__) . '/js/script.js');
		wp_enqueue_script('sweepstakes-default-js');
		wp_localize_script( 'sweepstakes-default-js', 'SWEEPSTAKES', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'img' => plugins_url('', __FILE__) . '/images/ajax-loader'
		));	
		
		self::add_popup();
		
	}
	
	static function add_popup(){
		if($_POST['signed-up'] == 'Y') :
			wp_register_style('sweepstakers_popupcss', plugins_url('', __FILE__) . '/popup/css/popup.css');
			wp_enqueue_style('sweepstakers_popupcss');
			
			wp_register_script('sweepstakers_popupjs', plugins_url('', __FILE__) . '/popup/js/popup.js');
			wp_enqueue_script('sweepstakers_popupjs');
		endif;
	}
	
	//replace the shotcode
	static function shotcode_replacement(){
		if($_POST['accept-the-term'] == 'Y' || $_POST['signed-up'] == 'Y') :
			$content = "<div style='border: 1px solid #1E90FF;' class='sweepstakes-signup-holder'>";
			$content .= '<h2 class="text-center">Sweepstakes Lucky Draw Entry Form</h2>';
			$content .= self::get_signup_form();
			$content .= "</div>";
			return $content;
						
		else:
			$content = "<div class='sweepstakes-content-termpage'>";
			$content .= '<h2 style="text-align: center; margin-top: 25px;">To Enter the Sweepstakes:</h2>';
			$content .= '<p style="text-align: center;">';
			$content .= 'View the <a href="#" id="dialog_opener" class="ui-state-default ui-corner-all">terms and conditions here</a> ';
			$content .= 'Once you agree to these terms, please proceed to the next page to enter your code for the sweepstakes drawing.';
			$content .= '</p>';
			$content .= self::get_accept_form();
			$content .= "</div>";
			$content .= self::get_terms_conditions();
		endif;
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
				
				$('.dont-have-luckynumber').click(function(){
					$('#show-form').toggle();
					return false;
				});
								
				
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
		$img = plugins_url('', __FILE__) . '/images/ajax-loader.gif';
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
					<td colspan='2'><span class='instruction-class'>Please enter the code you received in your giveaway. Do not use dashes or spaces. </span>	</td>
				</tr>
				
				<tr>
					<td><input type='submit' name='SignUp' value= 'SignUp'/> or
					<a class='dont-have-luckynumber' href='#'>  Don't have a lucky number? </a></td>
				</tr>
				
			</table>					
		</form>";
		
		$form .= "<div id='show-message' style='display:none'></div>";
		$form .= "<div id='ajax-loader' style='display:none;'><img src='$img' /></div>";
		
		$form .= "
			<form id='show-form' action='' method='post' style='display:none;'>
				<input type='hidden' name='email-code-submitted' value='Y' />
				<table class='signup-table'>
					<tr>
						<td><input size='25' type='text' id='email-for-get-code' name='email-for-get-code' value='' /></td>
						<td> <input type='button' id='send-code' value='get code' > </td>
					</tr>
				</table>
			</form>
		";
		return $form;
	}
	
	//ajax manipulation
	static function ajax_email_sending(){
		$email = trim($_REQUEST['email']);
		if(!is_email($email)){
			echo 1;
		}
		elseif(self::email_exist($email)){
			echo 2;
		}
		else{
			$body = self::generate_lucky_number();
			echo (self::send_email($email, 'Lucky Number', $body)) ? 3 : 4;
		}		
				
		exit;
	}
	
	/*
	 * return true if email exists
	 * */
	static function email_exist($email){
		global $wpdb;
		$tables = self::get_table_name();
		$table = $tables['email'];
		return $wpdb->get_var("SELECT number_id FROM $table WHERE email = '$email'");		
	}
	
	static function get_table_name(){
		global $wpdb;
		return array(
			'coupon' => $wpdb->prefix . 'lucky_numbers',
			'email' => $wpdb->prefix . 'registered_email'
		);
	}
	
	static function table_create(){
		global $wpdb;
		$tables = self::get_table_name();
		$lucky_number = $tables['coupon'];
		$email = $tables['email'];
		
		$sql_1 = "CREATE TABLE IF NOT EXISTS $lucky_number(
				`id` bigint unsigned NOT NULL AUTO_INCREMENT,
				`luckynumber` varchar(20) NOT NULL,
				 PRIMARY KEY(id),
				 UNIQUE(luckynumber)				
			)";
			
		$sql_2 = "CREATE TABLE IF NOT EXISTS $email(
				 `name` varchar(100) NOT NULL,
			     `email` varchar(100) NOT NULL,
			     `number_id` bigint NOT NULL,
			     UNIQUE(email, number_id)
			     
		)";
		
		if(!function_exists('dbDelta')) :
				include ABSPATH . 'wp-admin/includes/upgrade.php';
		endif;
			
		dbDelta($sql_1);
		dbDelta($sql_2); 
	
	}
	
	static function send_email($to, $subject, $body){
		$blogname = get_option('blogname');	
		$site_mail = get_option('admin_email');
		$headers = 'From : '.$blogname.' < '.$site_mail.' >' . "\r\n" .
			'Reply-To: '. $site_mail . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		if(!function_exists('wp_mail')) : 
			include ABSPATH . 'wp-includes/pluggable.php';
		endif;

		if(wp_mail($to, $subject, $body, $headers)){
			return true;
		}
		
		return false;
	}
	
	/*
	 * generates a unique lucky number and return the email message 
	 * */
	static function generate_lucky_number(){
		$a = mt_rand(10000, 99999);
		$b = mt_rand(10000, 99999);
		$number = $a . $b;
		if(self::number_exist($number)){
			return self::generate_lucky_number();
		}		
		
		global $wpdb;
		$tables = self::get_table_name();
		$table = $tables['coupon'];
		$wpdb->insert($table, array('luckynumber'=>$number), array('%s'));	
		$message = "Dear Visitor/User,\n Thank you for the request of a lucky number. \n Here is your lucky number. \n set 1 : $a \n set 2: $b \n";
		
		return $message;								
	}
	
	static function number_exist($number){
		global $wpdb;
		$tables = self::get_table_name();
		$table = $tables['coupon'];
		
		return $wpdb->get_var("SELECT id FROM $table WHERE luckynumber = '$number'");		
	}
	
	/*
	 * Handle signup and return message
	 * */
	static function handle_signup(){
		if($_POST['signed-up'] == 'Y') :
			if(self::email_exist(trim($_POST['sw-email']))){
				self::$error[] = "Email Address is already registered!";
			}
			elseif(self::number_used(trim($_POST['sw-code-first']) . trim($_POST['sw-code-last']))){
				self::$error[] = "The lucky number has already been used!";
			}
			else{
				$id = self::number_exist(trim($_POST['sw-code-first']) . trim($_POST['sw-code-last']));
				if($id){
					if(is_email(trim($_POST['sw-email']))){
						global $wpdb;
						$tables = self::get_table_name();
						$table = $tables['email'];
						$wpdb->insert($table, array('name'=>trim($_POST['sw-name']), 'email'=>trim($_POST['sw-email']), 'number_id'=>$id), array('%s', '%s', '%d'));
						self::$success[] = self::get_success_message();
					}
					else{
						self::$error[] = "Invalid Email";
					}
				}
				else{
					self::$error[] = "In valid Lucky Number!";
				}
			}
		endif;
	}
	
	/*
	 * if number is used
	 * */
	static function number_used($number){
		global $wpdb;
		$tables = self::get_table_name();
		$table1 = $tables['coupon'];
		$table2 = $tables['email'];	
		$id = $wpdb->get_var("SELECT id FROM $table1 WHERE luckynumber = '$number'");
		return $wpdb->get_var("SELECT email FROM $table2 WHERE number_id = '$id'");			
	}
	
	
	//returns the successful message
	static function get_success_message(){
		$info = self::get_sweep_info();
		return $info['confirm'];
	}
	
	/*
	 * popuphtml
	 * */
	static function PopupHTML(){
		if($_POST['signed-up'] == 'Y') :
        ?>
			<div id="popup">
				   <div style="display:none;" id="popup-content" class="window"> 
						  <?php
								if(count(self::$error)>0){
									echo "<div class='email-error'>";
									foreach(self::$error as $err){
										echo "<p>$err</p>";
									}
									echo "</div>";
								}
								else{
									echo "<div class='email-success'>";
									foreach(self::$success as $suc){
										echo "<p>$suc</p>";
									}
									echo "</div>";
								}
						  ?>
							 <a href="#" class="close"></a>
				  </div> 
					<div id="blanket"></div>
			</div>            
		<?
		endif; 
	}
	
}


//initializing everything
sweepstakes_management :: init();

