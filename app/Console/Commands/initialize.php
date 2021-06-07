<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
class initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $databaseName = Config::get('database.connections.' . Config::get('database.default') . '.database');
        $this->info('CREATE DATABASE ' . $databaseName . '...');
        if (!DB::statement('CREATE DATABASE IF NOT EXISTS ' . $databaseName)) {
            $this->error('database not created!');
            return 0;
        }
        $this->info('Migrate tables in database...');
        $this->call('migrate:refresh');

        $this->info('Generating starting equip types...');

        DB::table('TypesEquip')->insert([
            [
                'name' => 'TP-Link TL-WR74',
                'mask' => 'XXAAAAAXAA',
            ],
            [
                'name' => 'D-Link DIR-300',
                'mask' => 'NXXAAXZXaa',
            ],
            [
                'name' => 'D-Link DIR-300 S',
                'mask' => 'NXXAAXZXXX',
            ]
        ]);

        $this->info('Application ready!');
        return 0;
    }


}
