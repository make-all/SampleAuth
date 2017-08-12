# ServerAuth Plugin

This is an authentication plugin to authorize users based on prior authentication by the web server.  Primary use-cases are where there is a single-sign-on authentication method, which is handled by web server authentication modules.  For example mod_auth_kerberos, mod_auth_openidc and similar Apache modules, or equivalents for nginx and other servers.

The authentication mechanism implemented by this plugin works as follows:
- username is extracted from the REMOTE_USER server provided variable.
- if the user exists, they are auto-signed in without requiring a password.
- if the user does not exist, they can be optionally auto-created.
- when auto creating users, they can optionally use other server variables to populate realname and email address (if the server authentication module can provide such info).

Users can't manage or use passwords that are stored in the MantisBT database when this authentication plugin is used.

## Configuration Options

- Auto Create Users - set this to allow this plugin to create users automatically. Default is disabled.
- Email Header - set this to the name of a header the server passes the email address as.  Default is AUTHORIZE_mail.
- Real Name Header - set this to the name of a header the server passes the real name as.  Default is AUTHORIZE_name.

## Example Apache configuration for ActiveDirectory

ActiveDirectory is a user directory using kerberos and LDAP, commonly available in corporate environments with mainly Windows clients.
An Apache server can be configured with mod_auth_kerb to authenticate against an ActiveDirectory server for single-sign-on. This configuration is quite complex, and there are already pages that describe it, so I will not go into detail here.

In addition to kerberos, you can also get further information about the user from LDAP, but the Require condition in Apache has to include checking some information that is only available from LDAP, otherwise it will skip the LDAP step.

    AuthType Kerberos
	...
	KrbLocalUserMapping On
	AuthnzForceUsernameCase Lower
	AuthLDAPURL "ldap://activedir.server.name/dc=domain,dc=name?sAMAccountName,name,mail" NONE
	AuthLDAPBindDN "cn=user,ou=group,dc=domain,dc=name"
	AuthLDAPBindPassword "password"
	Require ldap-attribute objectClass=person

Note the `,name,mail` on the end of `AuthLDAPURL`.  That causes Apache to include `AUTHORIZE_name` and `AUTHORIZE_mail` headers with those fields from LDAP.  The `Require ldap-attribute objectClass=person` line is basically equivalent to `Require valid-user`, but forces a check against LDAP so the name and mail headers can be populated.  Alternatively, you can use `ldap-group` and/or `ldap-user` conditions to limit access.

Tip: you can use `RequestHeader set AUTHORIZE_name %{ENV_VAR_name}e` to rewrite environment variables into headers if the auth plugin makes user information available in environment variables.

## Dependencies
MantisBT v2.4.0.
