<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class MysqlToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clone MysqlDb data to MongoDb';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $remove_id_column = $this->confirm('Do You Wish To Keep The Mysql Id Column ? "keep it if you want to resolve the models relations"');

        if ($this->confirm('Mongo DataBase Name='.DB::getMongoDB()->getDatabaseName().' Will Be Removed To Avoid Any Duplication')) {

            // mysql stuff
            $mysql_connection = DB::connection('mysql');
            $mysql_db_name    = $this->ask('Whats The Mysql Db Name ?');
            $tables           = $mysql_connection->select('SHOW TABLES');
            $mysql_tables     = 'Tables_in_'.$mysql_db_name;

            // drop mongo db first
            DB::getMongoDB()->drop();

            foreach ($tables as $one) {

                // extract the table name
                $name = $one->$mysql_tables;

                // get the table data
                $query = $mysql_connection->table($name)->get()->toArray();

                // create the collection even if table was empty
                if (empty($query)) {
                    DB::getMongoDB()->createCollection($name);
                }

                // create the collection & insert data to mongodb one by one
                else {
                    foreach ($query as $item) {

                        // turn into array
                        $arr = (array) $item;

                        // remove the id column
                        if ( ! $remove_id_column) {
                            $cln = array_shift($arr);
                        }

                        // insert into mongo
                        DB::table($name)->insert($arr);
                    }
                }
            }
        }
    }
}
