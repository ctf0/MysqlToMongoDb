<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class MysqlToMongoPivot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate:pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resolve external foreign ids';

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
        $modelOne   = $this->ask('The\Namespace\ModelOne ? ex.App\\Post');
        $modelTwo   = $this->ask('The\Namespace\ModelTwo ? ex.App\\Tag');
        $tableName  = $this->ask('The Pivot Table Name ? ex.post_tag');
        $method     = $this->ask('The Pivot Method Name In The "First" Model ? ex.tags');
        $drop_pivot = $this->confirm('Do You Wish To Keep The Pivot Collection on Finish ?');

        $field_name_one = snake_case(class_basename($modelOne)).'_id';
        $field_name_two = snake_case(class_basename($modelTwo)).'_id';

        $collection = DB::table($tableName)->get();

        foreach ($collection as $item) {
            $resolveOne = $modelOne::where('id', $item[$field_name_one])->first();
            $resolveTwo = $modelTwo::where('id', $item[$field_name_two])->first()->_id;

            $resolveOne->$method()->attach($resolveTwo);
        }

        if ( ! $drop_pivot) {
            DB::getMongoDB()->dropCollection($tableName);
        }
    }
}
