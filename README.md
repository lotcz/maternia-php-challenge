## Prerequisites

- PHP 7.4
- composer

## Install dependencies

    composer require --dev phpstan/phpstan
	composer require --dev phpunit/phpunit ^9
    npm install --save-dev prettier @prettier/plugin-php

## Run static code analysis

    vendor\bin\phpstan analyse src tests --level 6

## Run tests

    vendor\bin\phpunit tests
