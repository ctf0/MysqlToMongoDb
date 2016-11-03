<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class MysqlToMongoMaintain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:migrate:maintain
                                {auth_db : the auth database name}
                                {auth_user : the auth username}
                                {auth_pass : the auth password}
                                {db_name : the db name to backup/restore}
                                {--y|show_output : display the cmnd output}
                                {--b|backup : backup the db}
                                {--r|restore : restore the db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'backup/restore ur mongo db';

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
        $auth_db   = $this->argument('auth_db');
        $auth_user = $this->argument('auth_user');
        $auth_pass = $this->argument('auth_pass');
        $db_name   = $this->argument('db_name');
        $output    = $this->option('show_output');

        // backup db
        if ($this->option('backup')) {
            $date = Carbon::now()->toDateString();
            $path = storage_path('app/db-backups/');

            if ( ! File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $process = new Process('mongodump -u '.$auth_user.' -p '.$auth_pass.' --authenticationDatabase='.$auth_db.' --db='.$db_name.' --dumpDbUsersAndRoles --archive='.$date.' --gzip');
            $process->setWorkingDirectory($path);

            if ($output) {
                $process->run(function ($type, $buffer) {
                    echo $buffer;
                });
            } else {
                $process->run();
            }

            $this->info('>>> file is saved @ '."{$path}{$date}".' <\<\<');
        }

        // restore db
        if ($this->option('restore')) {
            $path = storage_path('app/db-restore/');

            $this->comment('>>> file will be restored from '.$path.' <\<\<');

            if ( ! File::exists($path)) {
                return $this->error('"storage/app/db-restore/" couldnt be found');
            }

            $file_name = $this->ask('the file name to be restored ? ex. 2016-10-25');
            $full_path = "$path/$file_name";

            $process = new Process('mongorestore -u '.$auth_user.' -p '.$auth_pass.' --authenticationDatabase='.$auth_db.' --db='.$db_name.' --objcheck --restoreDbUsersAndRoles --archive='.$full_path.' --gzip --drop --stopOnError');

            $process->setWorkingDirectory($path);

            if ($output) {
                $process->run(function ($type, $buffer) {
                    echo $buffer;
                });
            } else {
                $process->run();
            }

            $this->info('All Done');
        }
    }
}
