# MysqlToMongoDb
a simple console cmnd to migrate data from mysql to mongodb, note that this cmnd doesnt do anything other than simply copying the tables from mysql to mongo, if you need more control i would suggest using http://mongify.com/

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

4- make sure that both of ur dbs have the same creds and that you can connect to both of them without any issues
* ex `.env`
```bash
DB_HOST=127.0.0.1
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

5- run it by `php artisan mongo:migrate`
