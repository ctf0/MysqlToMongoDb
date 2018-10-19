<h1 align="center">
    Mysql To MongoDb
    <br>
    <a href="https://packagist.org/packages/ctf0/mysql-to-mongodb"><img src="https://img.shields.io/packagist/v/ctf0/mysql-to-mongodb.svg" alt="Latest Stable Version" /></a> <a href="https://packagist.org/packages/ctf0/mysql-to-mongodb"><img src="https://img.shields.io/packagist/dt/ctf0/mysql-to-mongodb.svg" alt="Total Downloads" /></a>
</h1>

I made this tool as i was starting to learn mongodb and i couldnt find any decent alternative. <br/>
The tool is working as expected and even tested on a live working app that uses mysql, however i strongly recommend that you use this package with a grain of salt.

## Installation

- `composer require ctf0/mysql-to-mongodb`

- (Laravel < 5.5) add the service provider

    ```php
    'providers' => [
        ctf0\MysqlToMongoDb\MysqlToMongoDbServiceProvider::class,
    ]
    ```

## Usage

```bash
mongo:migrate            # clone mysql data to mongodb
mongo:migrate:pivot      # resolve pivot foreign ids (ManyToMany)
mongo:migrate:relation   # resolve foreign ids (OneToMany)
mongo:migrate:cleanup    # remove un-wanted field/collection from the db
mongo:migrate:maintain   # backup/restore mongo db (mongodump / mongorestore)
```

[Wiki](https://github.com/ctf0/MysqlToMongoDb/wiki/Usage)

## Notes

- if you havent installed mongodb yet, check http://wp.me/p4DYee-9Q
- make sure that you can connect to both of your dbs through laravel without any issues.
- the package assume that your **mysql** driver connection is `mysql` and your **default** driver connection is `mongodb`.
- the package **doesnt** recreate the table types from `mysql`, and its up to `mongodb` to decide at that point, however currently the below types gets converted on migration
    - `tinyint(1) => bool`
    - `timestamp => date`
    - `multi(OneToMany) => index`
    - `unique => index/unique/sparse`

        **having a field with the same name in 2 different collections will give an error, so as a-way-around the index is saved as (CollectionName_field)**

- all your app calls to `id` should be changed to `_id` except in view which is automatically converted through the model.
- `moloquent` use `string` for the relation linking/referencing, so when converting the ***foreign_ids*** to `ObjectId` now you will have `string` on one side and `ObjectId` on the other which will cause lots of trouble, so its kept as **string**.

## ToDo

* Find Away To Add Data In Bulk Instead Of One By One.
* Upload Db Backup To S3.
* Make A Small GUI For Easier Migration.
