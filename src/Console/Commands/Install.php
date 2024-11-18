<?php

namespace KarolGolec\LaravelHosting\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-hosting:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the laravel-hosting package';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): void
    {
        $this->checkExistsDeployPhp();

        $hostingProvider = $this->choiceHostingProvider();

        match ($hostingProvider) {
            'SeoHost.pl' => $this->installSeoHostPl(),
        };

        $this->info('Now you can run the "dep deploy" command');
    }

    private function checkExistsDeployPhp(): void
    {
        if (file_exists(base_path('deploy.php'))) {
            $this->error('Deploy is already installed. Remove the deploy.php file');
            exit(1);
        }
    }

    private function choiceHostingProvider()
    {
        $hostingProvider = $this->choice(
            'What hosting provider?',
            ['SeoHost.pl']
        );

        if (!$hostingProvider) {
            $this->error('A hosting provider has not been selected');
            exit(1);
        }

        $this->info($hostingProvider .' hosting provider selected');

        return $hostingProvider;
    }

    private function installSeoHostPl(): void
    {
        $content = File::get(__DIR__.'/../../../deploy_stubs/seohostpl.php');

        $replace = [
            '{{APPLICATION_NAME}}' => $this->ask('Application name?', config('app.name')),
            '{{REPOSITORY}}' => $this->askRepository(),
            '{{HOST_NAME}}' => $this->ask('Server IP address?', ''),
            '{{HOST_USER}}' => $this->ask('What is the user of the server?', 'srv00000'),
            '{{SSH_PRIVATE_KEY}}' => $this->ask('What is the location of the SSH private key?', '~/.ssh/id_ed25519'),
            '{{DOMAIN}}' => $this->askDomain(),
        ];

        $content = str_replace(array_keys($replace), array_values($replace), $content);

        File::put(base_path('deploy.php'), $content);

         $this->confirm('Do you set LARAVEL_HOSTING_QUEUE_ENABLED in the .env file to true?', true)
             ? $this->setEnvironmentValue('LARAVEL_HOSTING_QUEUE_ENABLED', 'true')
             : $this->setEnvironmentValue('LARAVEL_HOSTING_QUEUE_ENABLED', 'false');

        Artisan::call('config:clear');
    }

    private function askRepository(): string
    {
        $composerName = File::json(base_path('composer.json'))['name'] ?? '';
        return $this->ask('Repository?', 'git@github.com:'.$composerName.'.git');
    }

    private function setEnvironmentValue(string $envKey, string $envValue): void
    {
        $envFile = app()->environmentFilePath();

        $contents = File::get($envFile);

        if (Str::contains($contents, $envKey)) {

            $oldValue = explode("\n", explode("{$envKey}=", $contents)[1])[0] ?? '';

            $contents = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $contents);

            File::put($envFile, $contents);

        } else {

            File::append($envFile, "{$envKey}={$envValue}\n");
        }

    }

    private function askDomain()
    {
        $domain = explode('://', config('app.url'))[1] ?? '';
        return $this->ask('What is the application domain in the hosting?', $domain);
    }
}
