<?php
namespace MemoryFile;

class Service
{
    protected $repositoryPath;

    public function setRepositoryPath($path)
    {
        $this->repositoryPath = $path;

        return $this;
    }

    public function __construct($repositoryPath)
    {
        $this->setRepositoryPath($repositoryPath);

        $this->log = LoggerFactory::newLogger($repositoryPath);
    }

    public function import($path)
    {
        //
    }
}

/* End of file */
