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

        if ($this->confirm('DB_NAME='.DB::getMongoDB()->getDatabaseName().' Will Be Removed To Avoid Any Duplication')) {

            // drop the db first
            DB::getMongoDB()->drop();

            // get all table names from mysql
            $mysql_connection = DB::connection('mysql');
            $tables           = $mysql_connection->select('SHOW TABLES');

            foreach ($tables as $one) {

                // extract the table name
                $name = $one->Tables_in_homestead;

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
