# Sainsbury's Product Scraper #

A console application that scrapes the Sainsbury’s grocery site - Ripe Fruits page and returns a JSON array of all the products on the page.

## Requirements ##

* PHP v5.5 
* Composer

## Setup ##

1. Clone the project.
2. Run `composer install`

## Command ##

Run `php console scrape:products` to scrape all the products from the Ripe Fruits page.

## Tests ##

Run `vendor/bin/phpunit app/tests/` 