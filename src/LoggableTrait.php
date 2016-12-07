<?php
namespace MemoryFile;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

trait LoggableTrait
{
    public function getLog($name = 'import')
    {
        if (! $this->repositoryPath) {
            throw new \Exception('No repositoryPath set for LoggableTrait::getLog');
        }

        if (! isset($this->log)) {
            $this->log = [];
        }

        $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '_', $name);

        if (! array_key_exists($name, $this->log)) {
            $this->log[$name] = new Logger($name);
            $this->log[$name]->pushHandler(new RotatingFileHandler($this->repositoryPath . '/' . $name . '.log', Logger::WARNING));
        }

        return $this->log[$name];
    }
}

/* End of file */