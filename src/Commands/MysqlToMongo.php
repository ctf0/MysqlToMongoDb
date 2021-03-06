<?php

namespace ctf0\MysqlToMongoDb\Commands;

use Carbon\Carbon;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToMongo extends Command
{
    protected $indexes = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate
                            {MysqlDb : the mysql database name ex. homestead}
                            {--y|keep_id : keep the ID column, keep it if you want to resolve the models relations}';

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
        $mysql_tables     = 'Tables_in_' . $this->argument('MysqlDb');
        $remove_id_column = $this->option('keep_id');

        if ($this->confirm('Mongo DataBase Name=' . DB::getMongoDB()->getDatabaseName() . ' Will Be Removed To Avoid Any Duplication')) {
            // mysql stuff
            $mysql_connection = DB::connection('mysql');
            $tables           = $mysql_connection->select('SHOW TABLES');

            // drop mongo db first
            DB::getMongoDB()->drop();

            // tables loop
            foreach ($tables as $one) {
                // extract the table name
                $name = $one->$mysql_tables;

                // get the table data
                $query = $mysql_connection->table($name)->get()->toArray();

                // create the collection even if the table was empty
                if (empty($query)) {
                    DB::getMongoDB()->createCollection($name);
                }

                // data loop
                else {
                    foreach ($query as $item) {
                        // turn into array
                        $arr = (array) $item;

                        // get the table columns so we can change its type
                        $columns = $mysql_connection->select(DB::raw('SHOW COLUMNS FROM ' . $name . ''));

                        /*
                         *
                         * Columns Loop
                         * here we change the column/field type before saving it to mongo
                         * add more conditions for extra fields
                         *
                         */
                        for ($i = 0; $i < count($columns); ++$i) {
                            // bool
                            if ($columns[$i]->Type == 'tinyint(1)') {
                                $arr[$columns[$i]->Field] = (bool) $arr[$columns[$i]->Field];
                            }

                            // date
                            if ($columns[$i]->Type == 'timestamp') {
                                $stamp                    = Carbon::parse($arr[$columns[$i]->Field])->timestamp;
                                $arr[$columns[$i]->Field] = new UTCDateTime($stamp * 1000);
                            }

                            // unique
                            if ($columns[$i]->Key == 'UNI') {
                                $indexes[] = $columns[$i]->Field;
                            }
                        }

                        // remove the id column
                        if (!$remove_id_column) {
                            array_shift($arr);
                        }

                        // insert into mongo
                        DB::table($name)->insert($arr);

                        if (!empty($indexes)) {
                            foreach ($indexes as $one) {
                                DB::table($name)->raw()->createIndex([$one => 1], ['unique' => true, 'sparse' => true, 'name' => "{$name}_{$one}"]);
                            }
                        }
                    }
                }
            }

            $this->info('All Done');
        }

        $this->info('Nothing Happened');
    }
}
