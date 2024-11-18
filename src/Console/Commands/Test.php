<?php

namespace KarolGolec\LaravelHosting\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-hosting:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing worker performance';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): void
    {
        dispatch(function () {

            Cache::set('laravel-hosting-testing', true);
        });


        foreach (range(1, 100) as $attempt){

            if (Cache::get('laravel-hosting-testing')){

                Cache::forget('laravel-hosting-testing');

                $this->info('Queue worker successfully operates on this hosting');
                exit(0);
            }

            $this->info('Waiting for a response from an worker...');

            sleep(1);
        }

        $this->error('Time has elapsed for answer. The test was not completed correctly.');
        exit(1);
    }
}
