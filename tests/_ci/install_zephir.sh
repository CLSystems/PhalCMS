#!/bin/sh
set -ex
sudo apt-get install git gcc make re2c php php-json php-dev libpcre3-dev build-essential
git clone https://github.com/jbboehr/php-psr.git
cd php-psr
phpize
./configure
make
make test
sudo make install
cd ..
