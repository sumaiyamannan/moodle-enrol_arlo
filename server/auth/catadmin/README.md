# auth_catadmin

This branch is only for Moodle 3.5 and up.
Use the 27_34STABLE branch for 2.7-3.4. Take note of the auth_saml2 version you install as it must match what is mentioned in the README.md for the branch you are installing.

auth_catadmin contains large parts of auth_saml2 since it is a copy of it, just with the bits we don't need because the two plugins can share them removed.
 
auth_catadmin allows us run additional SSO logins without interfering with auth_saml2 settings or data.

It requires auth_saml2 version 2020082101 to work.
