<?php

namespace ctf0\MysqlToMongoDb\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToMongoCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate:cleanup
                                {items* : the items to be removed ex. id, migrations, etc...}
                                {--f|field : if the items type is a field}
                                {--c|collection : if the items type is a collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove un-wanted fields/collections';

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
        $items = $this->argument('items');

        // remove field
        if ($this->option('field')) {
            // get all collections/tables
            $tables = DB::getMongoDB()->listCollections();

            // remove the field from each collection
            foreach ($tables as $one) {
                $name = $one->getName();
                foreach ($items as $item) {
                    DB::table($name)->unset($item);
                }
            }
        }

        // remove collection/table
        if ($this->option('collection')) {
            foreach ($items as $item) {
                DB::getMongoDB()->dropCollection($item);
            }
        }
    }
}
