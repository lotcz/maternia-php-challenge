## Prerequisites

- PHP 7.4
- composer

## Install dependencies

    composer install
    npm install

## Run static code analysis

    vendor\bin\phpstan analyse src tests --level 6

## Run tests

    vendor\bin\phpunit tests
