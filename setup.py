from setuptools import setup
import os

try:
    reqs = open(os.path.join(os.path.dirname(__file__),'requirements.txt')).read()
except (IOError, OSError):
    reqs = ''

setup(name='django-external-auth',
      version="1.0",
      description='Django App to let external software (dokuwiki) check the auth based on session-cookie',
      long_description="",
      author='Robert Weidlich',
      author_email='portal@robertweidlich.de',
      url='https://github.com/cato-/django-external-auth',
      packages=['external_auth'],
      include_package_data=True,
      classifiers=[
          'Framework :: Django',
          ],
      )

