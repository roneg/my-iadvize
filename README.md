iAdvizeProject
==============

Process


> git clone project


install symfony

> php -r "readfile('https://symfony.com/installer');" > symfony


install composer

> php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

> php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { > echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

> php composer-setup.php

> php -r "unlink('composer-setup.php');"


> composer update


Rest API :


> http://localhost:8000/api/posts

> http://localhost:8000/api/posts?from=2017-05-12&to=2017-12-31

> http://localhost:8000/api/posts?author=Juju

> http://localhost:8000/api/posts/1

Launch command : 


> php bin/console app:getVieDeMerde




**_Much to learn, You still have._**
