import os
import codecs
from setuptools import setup

def read(*parts):
    filename = os.path.join(os.path.dirname(__file__), *parts)
    with codecs.open(filename, encoding='utf-8') as fp:
        return fp.read()


setup(name='django-external-auth',
      description='Django App to let external software (dokuwiki) check the auth based on session-cookie',
      use_scm_version=True,
      setup_requires=['setuptools_scm'],
      long_description=read('README.md'),
      author='Robert Weidlich',
      author_email='github@robertweidlich.de',
      url='https://github.com/cato-/django-external-auth',
      packages=['external_auth'],
      include_package_data=True,
      classifiers=[
          'Framework :: Django',
          ],
      )

