<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AlbumsLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'directory:link {disk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "public/storage/folderName" to "storage/app/folderName"';

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
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        if (file_exists(public_path('storage/'.$this->argument('disk')))) {
            return $this->error('The '."public/storage/{$this->argument('disk')}".' directory already exists.');
        }

        $this->laravel->make('files')->link(
            storage_path('app/'.$this->argument('disk')), public_path('storage/'.$this->argument('disk'))
        );

        $this->info("The [public/storage/{$this->argument('disk')}] directory has been linked.");
    }
}
