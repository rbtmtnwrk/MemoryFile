<?php
namespace MemoryFile;

trait RepositoryPathTrait
{
    public function getRepositoryPath()
    {
        if (! $this->repositoryPath) {
            throw new \Exception('Invalid repositoryPath');
        }

        return $this->repositoryPath;
    }

    public function setRepositoryPath($repositoryPath)
    {
        $this->repositoryPath = $repositoryPath;

        return $this;
    }
}

/* End of file */