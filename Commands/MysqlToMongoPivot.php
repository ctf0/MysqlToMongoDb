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
    protected $signature = 'mongo:migrate:pivot
                                {modelOne : ex. App\\Post}
                                {modelTwo : ex. App\\Tag}
                                {pivotTable : pivot table name ex. post_tag}
                                {relation_method : the relation method on the first model ex. tags}
                                {--y|keep_pivot : Dont Remove The Pivot Table on Finish}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'resolve pivot table foreign ids';

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
        $modelOne   = $this->argument('modelOne');
        $modelTwo   = $this->argument('modelTwo');
        $tableName  = $this->argument('pivotTable');
        $method     = $this->argument('relation_method');
        $drop_pivot = $this->option('keep_pivot');

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
