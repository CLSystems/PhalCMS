# PhalCMS
A Content Management System based on Phalcon v4 and UIKit v3

## Requirements
- Apache
- PHP >= 7.2
- MySql >= 5.7
- Phalcon >= 4.0
- PHP ZIP extension
- PHP mod-rewrite
- <a href="https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx" rel="nofollow">Composer</a>

## Included
- <a href="https://github.com/CLSystems/php-registry">Php-registry</a>
- <a href="https://github.com/CLSystems/php-filter">Php-filter</a>

## Core features
- Multilingual
- Categories nested (Nested Set Model)
- Posts
- Comments
- Custom fields
- Menus
- Widgets
- Plugins
- Mailer
- Users
- Roles
- Custom admin path
- Template override
- Auto compress JS and CSS
- ...

## Core assets - a HUGE thank you to
- Jquery v1.12.4
- Jquery ui v1.12.1
- Jquery nested
- UIkit v3.3.2

# Installation for Development
### Clone this repo
```sh
git clone https://github.com/clsystems/phalcms.git
```

### Add current user to www-data group (to allow writing config.ini file during install)
```sh
sudo usermod -a -G www-data $USER
```

### Chmod permissions
```sh
cd phalcms
sudo chgrp -R www-data src
sudo chmod -R g+w src
sudo chmod -R g+s src
```

### Composer install
```sh
cd src
composer install
```
## Run website
```
Create a database with utf8mb4_unicode_ci collation.
Default database name is phalcms.

Make sure to setup your domain in such a way that 'src/public' is the webdirectory.
ie: "Document Root /var/www/example.tld/src/public"
```
Browse to http://example.tld and enjoy

# OR

## Build with Docker
```sh
cd ../
docker-compose build
docker-compose up -d
```

Browse http://localhost:8000 and enjoy