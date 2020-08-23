## Prerequisites

- PHP 7.4
- composer

## Install dependencies

    composer install

## Run static code analysis

    vendor\bin\phpstan analyse src tests --level 6

## Run tests

    vendor\bin\phpunit tests

## Prettier code formatting

Prettier will require **NPM** to install or perhaps IDE integration.

To install Prettier and PHP plugin globally, run this:

    npm install --global prettier @prettier/plugin-php

More information on https://prettier.io
