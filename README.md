Django external auth
====================

This project provides a mechanism to let DokuWiki do authentification against a
Django user data base. The [existing solution](https://www.dokuwiki.org/auth:django)
needs python on the server running DokuWiki and assumes that DokuWiki can access
the Django database.

Django external auth does not impose such restrictions by providing a small Django
app. This app exposes a minimal API, which is used by the provided DokuWiki Plugin.
The communication between the DokuWiki Plugin and the Django app is standard HTTP.

Installation
============

- Install django-external-auth into the virtualenv of your Django project

        pip install -e git://github.com/cato-/django-external-auth.git#egg=django-external-auth

- Add the views to your urls.py:

        import external_auth.views
        ...
        urlpatterns += patterns('',
            ...
            url(r'^extauth/trust_external/', external_auth.views.trust_external),
            url(r'^extauth/retrieve_groups/', external_auth.views.retrieve_groups),
        )

- Make sure that DokuWiki can read the session cookie of Django. For example use

        SESSION_COOKIE_DOMAIN = '.example.com'

  if your Django is on www.example.com and your DokuWiki in wiki.example.com

- Drop dokuwiki/extdjango.class.php into inc/auth/ of your DokuWiki Installation
- Set the following options in your conf/local.php

        $conf['authtype'] = 'extdjango';
        $conf['auth']['extdjango']['url'] = "https://www.example.com/extauth"


