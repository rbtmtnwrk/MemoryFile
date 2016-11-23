<?php
namespace MemoryFile;

class Repository
{
    protected $repositoryPath;
    protected $log;
    protected $lastDestination;
    protected $lastResult;

    use \MemoryFile\RepositoryPathTrait;

    public function getLastDestination()
    {
        return $this->lastDestination;
    }

    public function getLastResult()
    {
        return $this->lastResult;
    }

    private function createPath($path)
    {
        $subs  = explode('/', $path);
        $check = $this->getRepositoryPath();

        foreach ($subs as $sub) {
            $check .= '/' . $sub;
            ! file_exists($check) && mkdir($check, 0777);
        }
    }

    public function add($folder, $splFileInfo)
    {
        $this->createPath($folder);

        $destination = $this->getRepositoryPath() . '/' . $folder . '/' . $splFileInfo->getBaseName();

        $dupe = file_exists($destination) && (sha1_file($destination) == sha1_file($splFileInfo->getPathName()));

        (! $dupe) && copy($splFileInfo->getPathName(), $destination);

        $this->lastDestination = $destination;
        $this->lastResult = ! $dupe;

        return $this;
    }
}