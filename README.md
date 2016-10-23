# MysqlToMongoDb
A Console Commands To Help With Data Migration From **mysql** To **mongodb**.

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
];
```

3- from the root of your project run `composer dump-autoload`

## # Usage
now you have 4 new cmnds.

```bash
mongo:migrate            # clone mysql data to mongodb
mongo:migrate:pivot      # resolve pivot foreign ids (ManyToMany)
mongo:migrate:relation   # resolve foreign ids (OneToMany)
mongo:migrate:cleanup    # remove un-wanted field/collection from the db
```

1- mongo:migrate
>  - drop the db if exist b4 doing anything to avoid issues
>  - clone tables one by one from mysql to mongodb
>  - an option to remove the `id` column if not needed.

2- mongo:migrate:pivot
>  - add the **First** model name, the **Second** model name, the pivot table name, the relation method we will attach by.
>  - choose to remove the pivot collection on finish or not.
>  - resolve the relation foreign ids

3- mongo:migrate:relation
>  - add the fields you want to resolve ex.`post_id user_id etc_id`
>  - go through each collection/table and resolve the foreign ids through its corresponding table name. `posts users etcs`

4- mongo:migrate:cleanup
>  - choose **Field** or **Collection**
    - if `field` then remove it from all collections
    - if `collection` then drop it

## # Notes
- the package assume that your **mysql** driver connection is `mysql` and your **default** driver connection is `mongodb`.
- note that the package **doesnt** recreate the table types from `mysql`, and its up to `mongodb` to decide at that point, so make sure to cast your attributes to avoid issues.
- now all your db calls to `id` should be `_id` except in view which is automaticlly converted through the model.

# ToDo

* [ ] Find Away To Add Data In Bulk Instead Of One By One.
* [x] Find Away To Update Date Fields With Timezone. "`created_at/updated_at/deleted_at` are now saved as Date type"
* [ ] Update Field Type After Migration. "any help/PR is welcomed".
* [ ] Turn into Package.
