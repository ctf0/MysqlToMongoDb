# MysqlToMongoDb

A Console Commands To Help With Data Migration From **mysql** To **mongodb**.

- the package is constantely being updated to add new featuers/update current workflow, so if you have any ideas plz make a ticket or better yet send me a PR 🎁.

## # PreRequisites

- install https://moloquent.github.io/master/#moloquent
- if you havent installed mongodb yet, check http://wp.me/p4DYee-9Q
- make sure that you can connect to both of your dbs through laravel without any issues.

## # Installation

1- copy files from `Commands` to `app/Console/Commands`

2- add the below to `app/Console/Kernel.php`

```php
protected $commands = [
    // ...
    Commands\MysqlToMongo::class,
    Commands\MysqlToMongoPivot::class,
    Commands\MysqlToMongoCleanUp::class,
    Commands\MysqlToMongoRelation::class,
    Commands\MysqlToMongoMaintain::class,
];
```

3- from the root of your project run `composer dump-autoload`

## # Usage

now you have 5 new cmnds.

```bash
mongo:migrate            # clone mysql data to mongodb
mongo:migrate:pivot      # resolve pivot foreign ids (ManyToMany)
mongo:migrate:relation   # resolve foreign ids (OneToMany)
mongo:migrate:cleanup    # remove un-wanted field/collection from the db
mongo:migrate:maintain   # backup/restore mongo db (mongodump / mongorestore)
```

**1-** `mongo:migrate <MysqlDb> --keep_id`
>  - drop the db on mongo if exist b4 to avoid issues
>  - clone tables one by one from mysql to mongodb
>  - choose to remove the `id` column or not.

**2-** `mongo:migrate:pivot <modelOne> <modelTwo> <pivotTable> <relation_method> --keep_pivot`
>  - resolve the relation foreign ids
>  - choose to remove the `pivot collection` or not.

**3-** `mongo:migrate:relation <fields>`
>  - add the fields you want to resolve ex.`post_id user_id etc_id`
>  - go through each collection/table and resolve the foreign ids through its corresponding table name. `posts users etcs`

**4-** `mongo:migrate:cleanup <items>`
>  - choose to remove those items as **Field** or **Collection**
    - if `Field` then remove it from all collections
    - if `Collection` then drop it

**5-** `mongo:migrate:maintain <auth_db> <auth_user> <auth_pass> <db_name> --show_output`
>  - choose to **Backup** or **Restore** *(in both the file is gzipd and archived for easier maintainability)*
    - for `Backup` file is saved at "storage/app/db-backups/"
    - for `Restore` file is restored from "storage/app/db-restore/"
        - the collection is droped b4 restoring

## # Notes

- the package assume that your **mysql** driver connection is `mysql` and your **default** driver connection is `mongodb`.
- the package **doesnt** recreate the table types from `mysql`, and its up to `mongodb` to decide at that point, however currently the below types are already converted on migration
    - `tinyint(1) => bool`;
    - `timestamp => date`;
    - `multi(OneToMany) => index`;

    - `unique => index/unique/sparse`;
    ###### the index is saved under (CollectionName_field) to avoid issues where you have the same field name in 2 different collections.

- all your app calls to `id` should be changed to `_id` except in view which is automatically converted through the model.
- `moloquent` use `string` for the linking, so when converting the foreign_ids to `ObjectId` now you will have `string` on one side and `ObjectId` on the other which will cause lots of trouble, so its kept as `string`.

# ToDo

* [x] Find Away To Update Date Fields With Timezone.
* [x] Update Field Type On Migration.
* [ ] Find Away To Add Data In Bulk Instead Of One By One.
* [ ] Upload Db Backup To S3.
* [ ] Make A Small GUI For Easier Migration.
* [ ] Turn into Package.
