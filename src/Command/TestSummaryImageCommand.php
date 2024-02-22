<?php

namespace App\Command;

use App\Exception\FalseException;
use App\Exception\FileNotExistsException;
use App\Utils\File\FileManagerInterface;
use App\Utils\Helper\Parser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:test:summary-image', description: 'Generate phpunit summary image.')]
final class TestSummaryImageCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly FileManagerInterface $fileManager,
        private readonly Parser $parser
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $lines = explode("\n", $this->fileManager->read('public/coverage.txt'));
        } catch (FileNotExistsException $exception) {
            $io->error(sprintf('File "%s" is not exists.', $exception->getPath()));

            return Command::FAILURE;
        }

        try {
            $im = imagecreatetruecolor(280, 80) ?: throw new FalseException();
            $i = 0;

            do {
                if ('' !== ($line = array_shift($lines))) {
                    imagestring($im, 10, 10, 10 + $i++ * 15, $line, $this->generateColorByPercentage($im, $line));
                }
            } while (count($lines));

            imagepng($im, $this->projectDir.'/public/coverage.png');

            $io->success('PHP unit summary is generated.');

            return Command::SUCCESS;
        } catch (\Throwable) {
            $io->error('You need gd extension.');

            return Command::FAILURE;
        }
    }

    private function generateColorByPercentage(\GdImage $im, string $line): int
    {
        if (!str_contains($line, '%')) {
            return imagecolorallocate($im, 255, 255, 255) ?: throw new FalseException();
        }

        $percentage = $this->parser->stringToFloat(explode('%', $line)[0]);

        return match (true) {
            $percentage >= 90 => imagecolorallocate($im, 0, 255, 0) ?: throw new FalseException(),
            $percentage >= 50 => imagecolorallocate($im, 255, 255, 0) ?: throw new FalseException(),
            default => imagecolorallocate($im, 255, 0, 0) ?: throw new FalseException()
        };
    }
}
