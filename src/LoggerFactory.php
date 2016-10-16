<?php
namespace MemoryFile;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class LoggerFactory
{
    protected $logger;

    public static function newLogger($repositoryPath)
    {
        $factory = new self;

        return $factory->create($repositoryPath);
    }

    public function create($repositoryPath)
    {
        $this->logger = new Logger('memory');
        $this->logger->pushHandler(new RotatingFileHandler($repositoryPath . '/memory.log', Logger::WARNING));

        return $this->logger;
    }
}