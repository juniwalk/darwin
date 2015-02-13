<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   Darwin
 * @link      https://github.com/juniwalk/darwin
 * @copyright Martin Procházka (c) 2015
 * @license   MIT License
 */

namespace JuniWalk\Darwin\Command;

use JuniWalk\Darwin\IO\Json;
use JuniWalk\Darwin\IO\Phar;
use Nette\Utils\Finder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * Path to default binary.
     *
     * @var string
     */
    const PATH = '/usr/local/bin/darwin';

    /**
     * Default file permissions.
     *
     * @var int
     */
    const MODE = 0744;


    /**
     * Path to temporary phar file..
     *
     * @var string
     */
    const TEMP = './darwin.phar';


    /**
     * Configure this command.
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install Darwin into $PATH directory');

        // Define arguments and options of this command with default values
        $this->addArgument('path', InputArgument::OPTIONAL, 'Override binary path', static::PATH);
        $this->addArgument('mode', InputArgument::OPTIONAL, 'Override file permissions', static::MODE);
    }


    /**
     * Command's entry point.
     *
     * @param InputInterface   $input   Input stream
     * @param OutputInterface  $output  Output stream
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Set input/output streams into instance
        $this->setInputOutput($input, $output);

        // Gather arguments and options of this command
        $path = $input->getArgument('path');
        $mode = $input->getArgument('mode');

        // Search for the *.php files without tests
        $files = Finder::findFiles('*.php')->from('./..')
            ->exclude('res', 'tests');

        // Get the number of found files
        $sizeof = iterator_count($files);

        // If there are no contents
        if (empty($sizeof)) {
            return null;
        }

        // Get new progress bar instance
        $bar = $this->getProgressBar($sizeof);

        // If the Phar already exists
        if (is_file(static::TEMP)) {
            // Try to unlink it
            @unlink(static::TEMP);
        }

        // Create new and empty Phar archive
        $phar = new Phar(static::TEMP, null, 'darwin.phar');

        // Iterate over the found files
        foreach ($files as $realpath => $file) {
            // Display path to file in the message
            // and advance progress bar to ne unit
            $bar->setMessage($realpath);
            $bar->advance();

            // Insert file into Phar
            $phar->add($file);
        }

        // Task has finished
        $bar->setMessage('<info>Darwin.phar generated.</info>');
        $bar->finish();

        // Set bootstrap file and compress data
        $phar->setStub($this->getBootstrap());
        $phar->compressFiles($phar::GZ);
        $phar = null; // Save the archive

        // Move the file to PATH and set mode
        if (!rename(static::TEMP, $path)) {
            throw new \RuntimeException('Failed to write the phar.');
        }

        // Set the file mode
        chmod($path, $mode);

        $output->writeln(PHP_EOL.'<info>Darwin has been successfuly installed.</info>');
    }


    /**
     * Get phar's bootstrap code.
     *
     * @return string
     */
    protected function getBootstrap()
    {
        // Return the contents of the bootstrap.php file
        return file_get_contents(__DIR__.'/../../res/bootstrap.php');
    }
}
