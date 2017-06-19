<?php

namespace ctf0\MysqlToMongoDb\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToMongoRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate:relation
                                {fields* : the fields to be resolved ex. role_id article_id etc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resolve foreign ids';

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
        // get the fields
        $fields = $this->argument('fields');

        // get all the tables in mongo
        $tables = DB::getMongoDB()->listCollections();

        foreach ($tables as $one) {
            // get the name of each collection
            $name = $one->getName();

            foreach ($fields as $field) {
                // get the table name of the field
                $str = str_plural(str_replace('_id', '', $field));

                // get the old & new ids from the field collection/table
                $collection = DB::table($str)->pluck('id', '_id');

                foreach ($collection as $new_id => $old_id) {
                    // replace the old id with the new one in the collection
                    DB::table($name)->where($field, $old_id)->update([
                        $field => $new_id,
                    ]);
                }

                DB::table($name)->raw()->createIndex([$field => 1]);
            }
        }
    }
}
