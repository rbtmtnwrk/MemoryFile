<?php
namespace MemoryFile;

use MemoryFile\FileSystem\DirnameFilter;
use MemoryFile\FileSystem\FilenameFilter;

class Service
{
    protected $repositoryPath;
    protected $transformer;
    protected $repository;

     use \MemoryFile\LoggableTrait;
     use \MemoryFile\RepositoryPathTrait;

    public static function create($repositoryPath)
    {
        $repository = new Repository;
        $service    = new Service(new Transformer, $repository->setRepositoryPath($repositoryPath));

        return $service->setRepositoryPath($repositoryPath);
    }

    public function __construct(
        Transformer $transformer,
        Repository $repository
    ) {
        $this->transformer   = $transformer;
        $this->repository    = $repository;
    }

    public function createIterator($path)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $filter    = new MemoryFile\FileSystem\DirnameFilter($directory, '/^(?!\.Trash)/');
        $filter    = new MemoryFile\FileSystem\FilenameFilter($filter, '/\.(?:jpeg|jpg|tiff|mov)$/');

        return new \RecursiveIteratorIterator($filter);
    }

    public function import($path)
    {
        //
    }
}

/* End of file */
