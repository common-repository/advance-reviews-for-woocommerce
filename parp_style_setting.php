<form method="post" class="parp_style">
<?php 
 if($_POST['parp_style_setting']){
			
		$parp_rating_bg_color= sanitize_text_field( $_POST['parp_rating_bg_color']);
		$parp_rating_color= sanitize_text_field( $_POST['parp_rating_color']);
		$parp_ttl_color= sanitize_text_field( $_POST['parp_ttl_color']);
		$parp_prctg_color= sanitize_text_field( $_POST['parp_prctg_color']);
		$parp_str_clr= sanitize_text_field( $_POST['parp_str_clr']);
		
		$parp_style_data=array('parp_rating_bg_color'=>$parp_rating_bg_color,
							'parp_rating_color'=>$parp_rating_color,
							'parp_ttl_color'=>$parp_ttl_color,'parp_prctg_color'=>$parp_prctg_color,
							'parp_str_clr'=>$parp_str_clr,
						);
		
				
		update_option('data_style_adv_review',$parp_style_data);
		 
		}
		
		$parp_style_data=get_option('data_style_adv_review');
		
		if(!empty($parp_style_data)){
			extract($parp_style_data);
		}
		 
		
?>
<h3>Styling Options</h3>
	<table class="form-table">
		<tbody>
		<tr class="user-user-login-wrap">
			<th><label for="parp_rating_bg_color">Rating bar background color:</label></th>
			<td><input type="text" class="parp_bg_color" value="<?php if(isset($parp_rating_bg_color)) { echo ($parp_rating_bg_color!='')?$parp_rating_bg_color:'#f4f4f4' ; }else{echo '#f4f4f4';}?>" id="parp_rating_bg_color" name="parp_rating_bg_color"></td>
		</tr>
		<tr class="user-user-login-wrap">
			<th><label for="parp_rating_color">Rating bar color:</label></th>
			<td><input type="text" class="parp_bg_color" value="<?php if(isset($parp_rating_color)) { echo ($parp_rating_color!='')?$parp_rating_color:'#a9709d' ; }else{echo '#a9709d';}?>" id="parp_rating_color" name="parp_rating_color"></td>
		</tr>
		<tr class="user-user-login-wrap">
			<th><label for="parp_ttl_color">Title color:</label></th>
			<td><input type="text" class="parp_bg_color" value="<?php if(isset($parp_ttl_color)) { echo ($parp_ttl_color!='')?$parp_ttl_color:'#141412' ; }else{echo '#141412';}?>" id="parp_ttl_color" name="parp_ttl_color"></td>
		</tr>
		<tr class="user-user-login-wrap">
			<th><label for="parp_prctg_color">Percentage value on rating bar color:</label></th>
			<td><input type="text" class="parp_bg_color" value="<?php if(isset($parp_prctg_color)) { echo ($parp_prctg_color!='')?$parp_prctg_color:'#fff' ; }else{echo '#fff';}?>" id="parp_prctg_color" name="parp_prctg_color"></td>
		</tr>
		<tr class="user-user-login-wrap">
			<th><label for="parp_str_clr">Star Color:</label></th>
			<td><input type="text" class="parp_bg_color" value="<?php if(isset($parp_str_clr)) { echo ($parp_str_clr!='')?$parp_str_clr:'#141412' ; }else{echo '#141412';}?>" id="parp_str_clr" name="parp_str_clr"></td>
		</tr>
		</tbody>
	</table>
	<input type="submit" value="Save Changes" class="button-primary" name="parp_style_setting"  style="float: left; margin-right: 10px;">
</form>