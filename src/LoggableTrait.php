<?php
namespace MemoryFile;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

trait LoggableTrait
{
    public function getLog($type = 'import')
    {
        if (! $this->repositoryPath) {
            throw new \Exception('No repositoryPath set for LoggableTrait::getLog');
        }

        if (! isset($this->log)) {
            $this->log = [];
        }

        if (! array_key_exists($type, $this->log)) {
            $this->log[$type] = new Logger($type);
            $this->log[$type]->pushHandler(new RotatingFileHandler($this->repositoryPath . '/' . $type . '.log', Logger::WARNING));
        }

        return $this->log[$type];
    }
}

/* End of file */