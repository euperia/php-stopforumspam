php-stopforumspam
=================

A PHP Library for validating email addresses and IP addresses against the stopforumspam service at
http://www.stopforumspam.com.

Installation
------------
Install using composer:

    php composer.phar update

Tests can be run using phpunit which will be installed in the `./vendor/bin/` directory:

    ./vendor/bin/phpunit

If you wish to use the provided Gulpjs build process to watch and run the tests, simply install the dependancies and run
gulp:

    npm install
    gulp

Usage
-----
To use the package you must make sure it is in your path.

    <?php

    use Euperia\Stopforumspam;

    $spamCheck = new Stopforumspam();
    $spamCheck->addEmail('sibleyjscxk@hotmail.com');
    if (false === $spam->check()) {
    //   fail
    }

Enjoy!
