<?php

namespace Hesto\Core\Commands;

use Hesto\Core\Traits\CanReplaceKeywords;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


abstract class AppendContentCommand extends InstallCommand
{
    use CanReplaceKeywords;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Get the destination path.
     *
     * @return string
     */
    abstract function getFiles();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $files = $this->getFiles();

        foreach ($files as $file) {
            $path = $file['path'];
            $fullPath = base_path() . $path;

            if($this->putContent($fullPath, $this->compileContent($fullPath, $file))) {
                $this->getInfoMessage($fullPath);
            }
        }

        return true;
    }

    /**
     * Compile content.
     *
     * @param $content
     * @return mixed
     */
    protected function compileContent($path, $file)
    {
        $string = $this->replaceNames($this->files->get($file['append']));

        $stub = $file['search'] . $string;

        $content = str_replace($file['search'], $stub, $this->files->get($path));

        return $content;
    }

    protected function getInfoMessage($filePath)
    {
        return $this->info('Content changed in: ' . $filePath);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force override existing files'],
        ];
    }
}
