<?php 
function parp_settings(){
	if(isset($_POST['parp_genaral_setting'])){
		
		$parp_plugin_enable= sanitize_text_field( $_POST['parp_plugin_enable']);
		$parp_sh_ttl= sanitize_text_field( $_POST['parp_sh_ttl']);
		$parp_spv_enable= sanitize_text_field( $_POST['parp_spv_enable']);
		$parp_upload_enable= sanitize_text_field( $_POST['parp_upload_enable']);
		$parp_upload_lmt= sanitize_text_field( $_POST['parp_upload_lmt']);
		
		$parp_gen_data=array('parp_plugin_enable'=>$parp_plugin_enable,
							'parp_sh_ttl'=>$parp_sh_ttl,'parp_spv_enable'=>$parp_spv_enable,
							'parp_upload_enable'=>$parp_upload_enable,
							'parp_upload_lmt'=>$parp_upload_lmt,
						);
		
				
		
		update_option('data_gen_adv_review',$parp_gen_data);
		
	}
	$parp_genral_data=get_option('data_gen_adv_review');
		
		if(!empty($parp_genral_data)){
			extract($parp_genral_data);
		}
	
?>	
<div>
		<?php $parp_tabs = sanitize_text_field( $_GET['parp_tabs'] );	?>
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a class="nav-tab <?php if($parp_tabs == 'gen' || $parp_tabs == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=parp-manager&amp;parp_tabs=gen">General Option</a>
			<a class="nav-tab <?php if($parp_tabs == 'style'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=parp-manager&amp;parp_tabs=style">Style Options</a>
			<a class="nav-tab <?php if($parp_tabs == 'prem'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=parp-manager&amp;parp_tabs=prem">Premium </a>
			<a class="nav-tab <?php if($parp_tabs == 'allp'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=parp-manager&amp;parp_tabs=allp">More Plugins </a>
		</h2>
		<?php 
			if($parp_tabs == 'gen' || $parp_tabs == ''){ ?>
				
				<form class="parp_genral" method="post">
				<h3>General</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top" class="">
								<th scope="row" class="titledesc">Enable Advance Review Plugin</th>
								<td class="forminp forminp-checkbox">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<label for="parp_plugin_enable">
											<input type="checkbox" name="parp_plugin_enable" id="parp_plugin_enable" <?php if(isset($parp_plugin_enable)){echo ($parp_plugin_enable==1)?'checked':'' ;}else{ echo 'checked';}?>  value="1"> 
										</label> 
									</fieldset>
								</td>
							</tr>
							<tr valign="top" class="">
								<th scope="row" class="titledesc">Show Title Field</th>
								<td class="forminp forminp-checkbox">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<label for="parp_sh_ttl">
											<input type="checkbox" name="parp_sh_ttl" id="parp_sh_ttl" <?php if(isset($parp_sh_ttl)){echo ($parp_sh_ttl==1)?'checked':'' ;}else{ echo 'checked';}?>  value="1"> 
										</label> 
									</fieldset>
								</td>
							</tr>
							<tr valign="top" class="">
								<th scope="row" class="titledesc">Show Percentage value on Rating bar</th>
								<td class="forminp forminp-checkbox">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<label for="parp_spv_enable">
											<input type="checkbox" name="parp_spv_enable" id="parp_spv_enable" <?php if(isset($parp_spv_enable)){echo ($parp_spv_enable==1)?'checked':'' ;}else{ echo 'checked';}?>  value="1"> 
										</label> 
									</fieldset>
								</td>
							</tr>
							<tr valign="top" class="">
								<th scope="row" class="titledesc">Show Attachment On Reviews</th>
								<td class="forminp forminp-checkbox">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<label for="parp_upload_enable">
											<input type="checkbox" name="parp_upload_enable" id="parp_upload_enable"  <?php if(isset($parp_upload_enable)){echo ($parp_upload_enable==1)?'checked':'' ;}else{ echo 'checked';}?> value="1"> 
										</label> 
									</fieldset>
								</td>
							</tr>
							
							<tr valign="top" class="" style="display:none" >
								<th scope="row" class="titledesc">Set uploading limit</th>
								<td class="forminp forminp-checkbox">
									<fieldset>
										<legend class="screen-reader-text"></legend>
										<label for="parp_upload_lmt">
											<input type="number" min="0" name="parp_upload_lmt" id="parp_upload_lmt"  value="<?php if(isset($parp_upload_lmt)){echo ($parp_upload_lmt!='')?$parp_upload_lmt:1 ;}else{ echo 1;}?>"> 
										</label> 
									</fieldset>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">

								</th>

								<td class="forminp forminp-color plugin-option">
									<div class="convert-reviews">
										<a href="<?php echo esc_url(add_query_arg("convert-reviews", "start")); ?>"
										   class="button convert-reviews"><?php _e("Convert reviews", "parp"); ?></a>

										<div style="display: inline-block; width: 65%; margin-left: 15px;"><span
												class="description"><?php _e("If this is the first time you install the Advanced Reviews plugin, or if you are using an older version prior to the 1.1.0, first you have to convert the older reviews if you want to use them.", "parp"); ?></span>
										</div>
									</div>
								</td>
							</tr>
							
						</tbody>
					</table>
					<input type="button" style="float: left; margin-right: 10px;" class="button button-info" value="Reset" id="parp_reset_value" name="parp_reset_value">
					<input type="submit" style="float: left; margin-right: 10px;" name="parp_genaral_setting" class="button-primary" value="Save Changes">
				</form>
			<?php }else if($parp_tabs=="style"){
			
					include_once('parp_style_setting.php');
				}else if($parp_tabs=="prem"){?>
			
			<?php 
			}
			
			if($parp_tabs == 'allp')
			{
				?>
				<style>
				iframe.more-plugin {
					min-height: 1000px;
					width: 100%;
				}

				.wrap{
					margin:0;
				}
				</style>
					<iframe class="more-plugin" src="http://plugins.snapppy.com/plugins.php"></iframe> 
				<?php
			}
			
			?>
</div>
	
<?php 	
}
?>