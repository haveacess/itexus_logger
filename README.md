Simple logger for using in your project.

Is realise of composer package.
You can use it by command:
````
composer install haveacess/logger
````

For start working with project
````
cp .env.dev.example .env
docker compose up
````

## Example of use

````
<?php

use Lumi\Logger;

Logger::debug('Connect to :part by :port', [
    'part' => 'database',
    'port' => '3306'
]);

Logger::info('User :name :lastName join in application', [
    'name' => 'John',
    'lastName' => 'Doe'
]);

Logger::notice('Example of notice message');
Logger::warning('Ho ho. You lose :thing', [
    'thing' => 'Apple'
]);

Logger::error('Oops. Room is burned');
Logger::critical('Help! Critical situation!');
Logger::alert('Can you call 911?');
Logger::emergency('His barely breathing..');
````