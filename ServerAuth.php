<?php
# Copyright (c) MantisBT Team - mantisbt-dev@lists.sourceforge.net
# Copyright (c) 2017 Mr Maker - make-all@users.github.com
# Licensed under the MIT license

/**
 * Server Auth plugin
 */
class ServerAuthPlugin extends MantisPlugin  {
	/**
	 * A method that populates the plugin information and minimum requirements.
	 * @return void
	 */
	function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config_page';

		$this->version = '0.2';
		$this->requires = array(
			'MantisCore' => '2.4.0',
		);

		$this->author = 'The Maker';
		$this->contact = 'make-all@users.github.com';
		$this->url = 'https://github.com/make-all/ServerAuth';
	}

	function config() {
		return array(
			"autocreate_users" => false,
			"email_var" => "AUTHORIZE_mail",
			"realname_var" => "AUTHORIZE_name"
		);
	}

	/**
	 * plugin hooks
	 * @return array
	 */
	function hooks() {
		$t_hooks = array(
			'EVENT_CORE_READY' => 'auto_login',
			'EVENT_AUTH_USER_FLAGS' => 'auth_user_flags',
		);

		return $t_hooks;
	}

	function auto_login() {
		if ( auth_is_user_authenticated() ) {
			return;
		}
		$t_username = $_SERVER['REMOTE_USER'];
		$t_user_id = empty($t_username) ? false : user_get_id_by_name( $t_username );
		if ( !$t_user_id ) {
			if (plugin_config_get('autocreate_users')) {
				$t_email = $_SERVER[plugin_config_get('email_var')];
				$t_realname = $_SERVER[plugin_config_get('realname_var')];
				user_create($t_username, auth_generate_random_password(), $t_email, auth_signup_access_level(), false, true, $t_realname);
			}
			return;
		}
		auth_login_user( $t_user_id );
	}

	function auth_user_flags( $p_event_name, $p_args ) {
		# Don't access DB if db_is_connected() is false.

		$t_username = $p_args['username'];

		$t_user_id = $p_args['user_id'];

		# If user is unknown, don't handle them.  Actually auto-provisioning
		# needs to be handled above - I'm not even sure if this is called
		# when the login page is bypassed by that.
		if( !$t_user_id ) {
			return null;
		}

		# If anonymous user, don't handle it.
		if( user_is_anonymous( $t_user_id ) ) {
			return null;
		}

		# use the custom authentication
		$t_flags = new AuthFlags();

		# Passwords managed externally for all users
		$t_flags->setCanUseStandardLogin( false );
		$t_flags->setPasswordManagedExternallyMessage( 'Passwords are no more, you cannot change them!' );

		# No one can use standard auth mechanism

		# Override Login page and Logout Redirect
		$t_flags->setCredentialsPage( helper_url_combine( plugin_page( 'login', /* redirect */ true ), 'username=' . $t_username ) );
		$t_flags->setLogoutRedirectPage( plugin_page( 'logout', /* redirect */ true ) );

		# No long term session for identity provider to be able to kick users out.
		$t_flags->setPermSessionEnabled( false );

		# Disable re-authentication, since we can't really force the server to
		# do anything.
		$t_flags->setReauthenticationEnabled( false );

		return $t_flags;
	}
}
