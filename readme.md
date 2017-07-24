# ServerAuth Plugin

This is an authentication plugin to authorize users based on prior authentication by the web server.  Primary use-cases are where there is a single-sign-on authentication method, which is handled by web server authentication modules.  For example mod_auth_kerberos, mod_auth_openidc and similar Apache modules, or equivalents for nginx and other servers.

The authentication mechanism implemented by this plugin works as follows:
- username is extracted from the REMOTE_USER server provided variable.
- if the user exists, they are auto-signed in without requiring a password.
- if the user does not exist, they can be optionally auto-created.
- when auto creating users, they can optionally use other server variables to populate realname and email address (if the server authentication module can provide such info).

Users can't manage or use passwords that are stored in the MantisBT database when this authentication plugin is used.

## Authentication Flags
The authentication flags events enables the plugin to control MantisBT core authentication behavior on a per user basis.
Plugins can also show their own pages to accept credentials from the user.

- `password_managed_elsewhere_message` message to show in MantisBT UI to indicate that password is managed externally.  If left blank or not set, the default message will be used.
- `can_use_standard_login` true then standard password form and validation is used, false: otherwise.
- `login_page` Custom login page to use.
- `credential_apge` The page to show to ask the user for their credential.
- `logout_page` Custom logout page to use.
- `logout_redirect_page` Page to redirect to after user is logged out.
- `session_lifetime` Default session lifetime in seconds or 0 for browser session.
- `perm_session_enabled` Flag indicating whether remember me functionality is enabled (ON/OFF).
- `perm_session_lifetime` Lifetime of session when user selected the remember me option.
- `reauthentication_enabled` A flag indicating whether reauthentication is enabled (ON/OFF).
- `reauthentication_expiry` The timeout to require re-authentication.  This is only applicable if `reauthentication_enabled` is set to ON.

If a flag is not returned by the plugin, the default value will be used based on MantisBT core configuration.

The plugin will get a user id and username within an associative array.  The flags returned are
in context of such user.  If user is not in db, then user_id will be 0, but username will be what
the user typed in the first login page that asks for username.

If plugin doesn't want to handle a specific user, it should return null.  Otherwise, it should
return the `AuthFlags` with the overriden settings.

## Dependencies
MantisBT v2.4.0.
