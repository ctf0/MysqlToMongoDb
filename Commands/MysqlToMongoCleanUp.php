<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class MysqlToMongoCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate:cleanup {items* : the items to be removed ex. id, migrations, etc...}';

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

        $choice = $this->choice(
            'Those "items" Are A Type Of ?',
            [
                '>>> Choose 1, 2 <\<\<',
                'Field',
                'Collection',
            ],
            1
        );

        // remove field
        if ($choice == 'Field') {

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
        if ($choice == 'Collection') {
            foreach ($items as $item) {
                DB::getMongoDB()->dropCollection($item);
            }
        }
    }
}
