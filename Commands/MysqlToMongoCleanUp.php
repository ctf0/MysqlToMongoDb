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
    protected $signature = 'mongo:migrate:cleanup';

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
        $choice = $this->choice('Select The Type You Want To Remove ?',
            ['>>> Choose 1, 2 <\<\<', 'Field', 'Collection'],
            1
        );

        // remove field
        if ($choice == 'Field') {
            $field = $this->ask('The Field To Be Removed ex.id');

            // get all collections/tables
            $tables = DB::getMongoDB()->listCollections();

            // remove the field from each collection
            foreach ($tables as $one) {
                $name = $one->getName();
                DB::table($name)->unset($field);
            }
        }

        // remove collection/table
        if ($choice == 'Collection') {
            $table = $this->ask('The Collection To Be Droped/Removed ex.users');

            DB::getMongoDB()->dropCollection($table);
        }
    }
}
