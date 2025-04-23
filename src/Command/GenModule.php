<?php
namespace Naroat\HyperfPackage\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GenModule extends HyperfCommand
{
    public function __construct()
    {
        parent::__construct('gen:module');
        $this->setDescription('Create new Module.');
    }

    public function configure()
    {
        parent::configure();
        $this->addArgument('name', InputArgument::REQUIRED, 'module name');
    }

    public function handle()
    {
        $name = $this->input->getArgument('name');

        //path
        $path = BASE_PATH . '/app/Module/' . $name;

        //create dir
        $dirs = [
            'Annotation',
            'Aspect',
            'Cache',
            'Command',
            'Controller',
            'Event',
            'Listener',
            'Middleware',
            'Model',
            'Request',
            'Service',
        ];

        if (file_exists($path) && is_dir($path)) {
            $this->warn('The directory exists.');
        } else {
            foreach ($dirs as $dir) {
                $pathFull = $path . '/' . $dir;
                @mkdir($pathFull, 0755, true);
            }
            $this->line('ok', 'info');
        }
    }


}