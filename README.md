# MysqlToMongoDb
this is a simple console cmnd to migrate data from mysql to mongodb

# PreRequisite
- install https://moloquent.github.io/master/#moloquent
- if you havent installed mongodb yet, check http://wp.me/p4DYee-9Q


# Usage
1- copy the `Commands/MysqlToMongo.php` file to `app/Console/Commands/MysqlToMongo.php`

2- add the below to `app/Console/Kernel.php`

```php
protected $commands = [
    // ...
    Commands\MysqlToMongo::class,
];
```
3- from the root of your project run `composer dump-autoload`

4- run it by `php artisan mongo:migrate`
