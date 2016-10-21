<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'migrate mysql data to mongodb';

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
        $mongo_connection_name = $this->ask('What Is The Mongodb Connection Name ? ex.mongodb');
        $remove_id_column      = $this->confirm('Do You Wish To Keep The Mysql Id Column ?');

        // get all table names
        $tables = DB::connection('mysql')->select('SHOW TABLES');

        foreach ($tables as $one) {

            // extract the name
            $name = $one->Tables_in_homestead;

            // get all the table data
            $sql = DB::connection('mysql')->table($name)->get()->toArray();

            // insert data to mongodb one by one
            foreach ($sql as $item) {

                // turn into array
                $arr = (array) $item;

                // remove the id column
                if (!$remove_id_column) {
                    $cln = array_shift($arr);
                }

                DB::connection($mongo_connection_name)->table($name)->insert($arr);
            }
        }
    }
}
