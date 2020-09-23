# Test task

## Subject
Write a script that will request the exchange rates for yesterday from the API of the Central Bank of the Russian Federation and return them in JSON format

*I made two versions of program: simple (one file, no classes, GET request) and complicated(OOP way, using SOAP)*

## Requirements
- Single script should run at minimun PHP 5.3, and its 'simplexml', 'json' extensions
- Complicated version needs PHP 7.4 version with 'soap', 'simplexml' and 'json' extensions and [Composer package manager](https://getcomposer.org/) to install dependencies

## How to use:
### Installation
Open console, choose a directory where project directory will be stored and run following commands:
```
git clone https://github.com/dzaporoz/test_10_2020.git test
cd test/complicated
composer install
```
### Running
##### WEB
Return to project root directory and run PHP self served
```
cd ..
php -S localhost:8000
```
A running scripts can be found at http://localhost and http://localhost/complicated (Ctrl+C to stop the server)

##### CLI
To run scripts in command line mode return to project root directory and run following commands:
```
php index.php
cd complicated && php index.php
```
