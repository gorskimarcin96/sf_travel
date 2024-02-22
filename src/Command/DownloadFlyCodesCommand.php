<?php

namespace App\Command;

use App\Utils\Crawler\FlyCodes\Wikipedia;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:download:fly-codes', description: 'Download fly codes.')]
final class DownloadFlyCodesCommand extends Command
{
    public function __construct(private readonly Wikipedia $wikipedia, private readonly string $projectDir)
    {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->wikipedia->getFlyCodes() as $flyCode) {
            $data[] = [
                'code' => $flyCode->getCode(),
                'city' => $flyCode->getCity(),
                'nation' => $flyCode->getNation(),
            ];
        }

        file_put_contents($this->projectDir.'/fly_codes.json', json_encode($data ?? [], JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
