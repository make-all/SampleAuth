<?php
# ServerAuth plugin - a MantisBT plugin for delegating login to the web server
#
# Copyright (C) 2017 The Maker - make-all@users.github.com
# Licensed under the MIT license

auth_reauthenticate();
access_ensure_global_level(config_get("manage_plugin_threshold"));

layout_page_header(plugin_lang_get('title'));
layout_page_begin();

print_manage_menu();
?>
<div class="cold-md-12 col-xs-12">
	<div class="space-10"></div>
	<div id="auth-config-div" class="form-container">
		<form action="<?php echo plugin_page('config') ?>" method="post" class="form-inline">
			<div class="widget-box widget-color-blue2">
				<div class="widget-header widget-header-small"><h4 class="widget-title lighter"><?php echo plugin_lang_get("config_title") ?></h4></div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<div class="table-responsive">
							<?php echo form_security_field("plugin_ServerAuth_config") ?>
							<table class="table table-striped table-bordered table-condensed"><tbody>
								<tr>
									<td class="category"><?php echo plugin_lang_get('autocreate_users') ?></td>
									<td><label><input type="checkbox" class="ace" name="autocreate_users" <?php echo ( plugin_config_get('autocreate_users') ? 'checked="checked" ' : '') ?>/><span class="lbl"></span></label></td>
								</tr>
								<tr>
									<td class="category"><?php echo plugin_lang_get('email_var') ?></td>
									<td><input class="form-control" maxlength="50" name="email_var" value="<?php echo plugin_config_get('email_var') ?>" /></td>
								</tr>
								<tr>
									<td class="category"><?php echo plugin_lang_get('realname_var') ?></td>
									<td><input class="form-control" maxlength="50" name="realname_var" value="<?php echo plugin_config_get('realname_var') ?>" /></td>
								</tr>
							</tbody></table>
						</div>
					</div>
					<div class="widget-toolbox passing-8 clearfix">
						<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo plugin_lang_get('action_update') ?>" />
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
layout_page_end();
