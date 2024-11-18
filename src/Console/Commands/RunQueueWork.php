<?php

namespace KarolGolec\LaravelHosting\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class RunQueueWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:work:hosting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command keeps always one worker on hosting.';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle(): void
    {
        $workerProcesses = $this->getWorkerProcesses();

        if (!$workerProcesses){
            $this->runQueueWorker();
        } else {
            $this->killToManyProcess($workerProcesses);
        }
    }

    private function getWorkerPIDs(): array
    {
        $workerPIDs = [];

        $result = Process::run('ps aux | grep "php artisan queue:work"');

        $processLines = explode("\n", $result->output());

        foreach ($processLines as $processLine) {

            $this->info($processLine);

            if (Str::contains($processLine, 'grep') || !trim($processLine) || Str::contains($processLine, 'queue:work:hosting')|| Str::contains($processLine, 'SCREEN')) {
                continue;
            }

            $workerPIDs[] = (int)preg_split("#\s+#", $processLine)[1];
        }

        $this->info('Detected PIDs: ' .implode(', ', $workerPIDs));

        return $workerPIDs;
    }

    /**
     * @throws Exception
     */
    private function getWorkerProcesses(): array
    {
        $workerPIDs = $this->getWorkerPIDs();

        return array_map(function ($workerPID) {
            $basePathWorker =  $this->getBasePathWorkerByPID($workerPID);

            return [
                'pid' => $workerPID,
                'base_path' => $basePathWorker,
            ];
        }, $workerPIDs);
    }

    /**
     * @throws Exception
     */
    private function getBasePathWorkerByPID(int $workerPID): ?string
    {
        if (Str::contains(PHP_OS, 'Linux')) {
            $result = Process::run('pwdx ' . $workerPID);
            $basePathWorker = trim(Str::replaceFirst($workerPID . ': ', '', $result->output()));

        } else if (Str::contains(PHP_OS, 'Darwin')) {
            $result = Process::run('lsof -p ' . $workerPID . ' | grep cwd | awk \'{print $9}\'');
            $basePathWorker = trim($result->output());

        } else {
            throw new Exception('Not implemented ' . PHP_OS);
        }

        $this->info('Base path worker: ' . $basePathWorker. ' '.$workerPID);

        if (!$basePathWorker) {
            return null;
        }

        $this->compareBasePath($basePathWorker);

        $this->info('Active process queue worker '.$workerPID.' with base path '.$basePathWorker);



        return $basePathWorker;
    }

    /**
     * @throws Exception
     */
    private function compareBasePath(string $basePathWorker): void
    {
        if ($basePathWorker != base_path()) {
            throw new Exception('Base path worker '. $basePathWorker .' does not match base path: ' . base_path());
        }
    }

    private function runQueueWorker(): void
    {
        $this->info('Running queue worker...');

        $phpBin = $this->getPhpBin();

        $command = 'screen -dmS laravel-queue-worker-'.Str::slug(config('app.name')). ' '.$phpBin.' artisan queue:work';

        $this->info('Run command '.$command);
        Process::run($command);
    }

    private function getPhpBin(): string
    {
        $this->info('Detected PHP binary: '.PHP_BINARY);
        return PHP_BINARY;
    }

    public function killToManyProcess(array $workerProcesses): void
    {
        foreach ($workerProcesses as $index => $workerProcess) {
            if ($index > 0){
                $this->info('Kill process PID: ' . $workerProcess['pid']);
                Process::run('kill 9 '.$workerProcess['pid']);
            }
        }
    }
}
