#!/bin/sh
set -ex
git clone https://github.com/jbboehr/php-psr.git
cd php-psr
phpize
./configure
make
make test
sudo make install
cd ..
