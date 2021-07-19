# davidlienhard/sessionhandler
🐘 php sessionhandler using a database connection

[![Latest Stable Version](https://img.shields.io/packagist/v/davidlienhard/sessionhandler.svg?style=flat-square)](https://packagist.org/packages/davidlienhard/sessionhandler)
[![Source Code](https://img.shields.io/badge/source-davidlienhard/sessionhandler-blue.svg?style=flat-square)](https://github.com/davidlienhard/sessionhandler)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/davidlienhard/sessionhandler/blob/master/LICENSE)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)
[![CI Status](https://github.com/davidlienhard/sessionhandler/actions/workflows/check.yml/badge.svg)](https://github.com/davidlienhard/sessionhandler/actions/workflows/check.yml)

## Setup

You can install through `composer` with:

```
composer require davidlienhard/sessionhandler:^1
```

*Note: davidlienhard/sessionhandler requires PHP 8.0*

## Example

```php
<?php declare(strict_types=1);
use DavidLienhard\SessionHandler\SessionHandler;

$sessionHandler = new SessionHandler($db);                          // create session handler object
session_set_save_handler($sessionHandler);                          // set custom session handler
session_start();                                                    // start session
```
## Requirements
There must be an existing database connection using [`davidlienhard/database`](https://github.com/davidlienhard/database). This database object must be passted to the constructor.

### Database-Table
A table with the name `sessions` with the following structure must exist in the database.

```sql
CREATE TABLE `sessions` (
  `sessionID` varchar(100) NOT NULL PRIMARY KEY,
  `sessionLastSave` int DEFAULT NULL,
  `sessionData` text
);
```

## License

The MIT License (MIT). Please see [LICENSE](https://github.com/davidlienhard/sessionhandler/blob/master/LICENSE) for more information.
