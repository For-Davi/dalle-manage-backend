<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class QueueWorkManager extends Command
{
    protected $signature = 'queue:work-all
                            {--tries=3 : Número de tentativas}
                            {--sleep=3 : Segundos de sleep entre jobs}
                            {--timeout=60 : Timeout de cada job}';

    protected $description = 'Start all queue workers';

    protected array $queues = [
        'emails' => 'emails',
        'payments' => 'payments',
        'notifications' => 'notifications',
    ];

    public function handle(): int
    {
        $this->info('Starting queue workers...');

        foreach ($this->queues as $name => $queue) {
            $this->startWorker($name, $queue);
        }

        $this->info('All workers started!');

        return self::SUCCESS;
    }

    private function startWorker(string $name, string $queue): void
    {
        $command = [
            'php', 'artisan', 'queue:work',
            '--queue='.$queue,
            '--tries='.$this->option('tries'),
            '--sleep='.$this->option('sleep'),
            '--timeout='.$this->option('timeout'),
        ];

        $process = new Process($command, base_path());
        $process->start();

        $this->line("  <info>✔</info> Worker <comment>{$name}</comment> iniciado (PID: {$process->getPid()})");
    }
}
