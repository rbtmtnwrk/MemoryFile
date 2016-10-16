<?php
namespace MemoryFile;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

trait LoggableTrait
{
    public function getLog()
    {
        if (! $this->repositoryPath) {
            throw new \Exception('No repositoryPath set for LoggableTrait::getLog');
        }

        $this->log = new Logger('memory');
        $this->log->pushHandler(new RotatingFileHandler($this->repositoryPath . '/memory.log', Logger::WARNING));

        return $this->log;
    }
}

/* End of file */