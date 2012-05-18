<div class="wrap">
	
	<?php
		if($_POST['sweep-submit'] == 'Y'){
			echo "<div class='updated'><p>info saved....</p></div>";
		}
	?>
	
	<h2>SweepStakes Management</h2>
	
	<form action="" method="post">
		<input type="hidden" name="sweep-submit" value="Y" />
		<table class="form-table">
			<tr>
				<td>Short Code to show <br/>the Sweepstakes in posts or page</td>
				<td colspan="2">
					<code>[wp_sweepstakes]</code>
				</td>
			</tr>
			
			<tr>
				<td colspan="3"><hr/></td>
			</tr>
						
			
			<tr>
				<td>Terms and conditons</td>
				<td colspan="2">
					<textarea rows="5" cols="75" name="sweep-terms"><?php echo stripcslashes($info['terms']); ?></textarea>
				</td>
			</tr>
						
			<tr>
				<td>Confirmation Page (popup)</td>
				<td colspan="2">
					<textarea rows="5" cols="75" name="sweep-confirm"><?php echo stripcslashes($info['confirm']); ?></textarea>
				</td>
			</tr>
			
			<tr>
				<td>Event Start (mm/dd/yyyy)</td>
				<td colspan="2">
					<input type="text" name="sweep-start" value="<?php echo $info['start']; ?>" />
				</td>
			</tr>
			<tr>
				<td>Event End (mm/dd/yyyy)</td>
				<td colspan="2">
					<input type="text" name="sweep-end" value="<?php echo $info['end']; ?>" />
				</td>
			</tr>
			
			
			<tr>
				<td><input class="button-primary" type="submit" value="save"></td>
			</tr>
			
		</table>
	</form>
	
</div>
