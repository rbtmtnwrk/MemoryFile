<?php
namespace MemoryFile;

class Repository
{
    protected $repositoryPath;
    protected $log;

    use \MemoryFile\LoggableTrait;
    use \MemoryFile\RepositoryPathTrait;

    private function createPath($path)
    {
        $subs  = explode('/', $path);
        $check = $this->getRepositoryPath();

        foreach ($subs as $sub) {
            $check .= '/' . $sub;
            ! file_exists($check) && mkdir($check, 0777);
        }
    }

    public function add($path, $splFileInfo)
    {
        $this->createPath($path);

        $destination = $this->getRepositoryPath() . '/' . $path . '/' . $splFileInfo->getBaseName();

        $same = file_exists($destination) && (sha1_file($destination) == sha1_file($splFileInfo->getPathName()));

        $same && $this->getLog()->warning('Memory Exists:' . $destination);

        (! $same) && copy($splFileInfo->getPathName(), $destination);

        return $this;
    }
}