<?php
namespace MemoryFile;

class Repository
{
    protected $repositoryPath;
    protected $log;

    public function setRepositoryPath($repositoryPath)
    {
        $this->repositoryPath = $repositoryPath;

        return $this;
    }

    public function __construct($repositoryPath)
    {
        $this->setRepositoryPath($repositoryPath);

        $this->log = LoggerFactory::newLogger($repositoryPath);
    }

    private function createPath($path)
    {
        $subs  = explode('/', $path);
        $check = $this->repositoryPath;

        foreach ($subs as $sub) {
            $check .= '/' . $sub;
            ! file_exists($check) && mkdir($check, 0777);
        }
    }

    public function add($path, $splFileInfo)
    {
        $this->createPath($path);

        $destination = $this->repositoryPath . '/' . $path . '/' . $splFileInfo->getBaseName();

        $same = file_exists($destination) && (sha1_file($destination) == sha1_file($splFileInfo->getPathName()));

        $same && $this->log->warning('Memory Exists:' . $destination);

        (! $same) && copy($splFileInfo->getPathName(), $destination);

        return $this;
    }
}