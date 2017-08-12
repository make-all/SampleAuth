<?php
# ServerAuth plugin - a MantisBT plugin for delegating auth to the web server.
#
# Copyright (C) 2017 The Maker - make-all@users.github.com
# Licensed under the MIT license

form_security_validate("plugin_ServerAuth_config");
access_ensure_global_level(config_get("manage_plugin_threshold"));

/* Avoid touching timestamp if no change. */
function maybe_set_option($name, $value) {
	if ($value != plugin_config_get($name)) {
		plugin_config_set($name, $value);
	}
}

maybe_set_option("autocreate_users", gpc_get_bool("autocreate_users", OFF));
maybe_set_option("email_var", gpc_get_string("email_var", OFF));
maybe_set_option("realname_var", gpc_get_string("realname_var", OFF));

form_security_purge("plugin_ServerAuth_config");
print_successful_redirect(plugin_page("config_page", true));
