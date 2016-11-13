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
        $this->transformer = $transformer;
        $this->repository  = $repository;
    }

    public function createIterator($path)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $filter    = new DirnameFilter($directory, '/^(?!\.Trash)/');
        $filter    = new FilenameFilter($filter, '/\.(?:jpeg|jpg|tiff|mov)$/');

        return new \RecursiveIteratorIterator($filter);
    }

    public function import($path)
    {
        $iterator = $this->createIterator($path);

        foreach ($iterator as $file) {
            $exif = @exif_read_data($file->getPathName(), 0, true);
            $memoryfile = $this->transformer->setExif($exif)->setSplFileInfo($file)->transform();

            if ($memoryfile['mime'] == 'directory') {
                continue;
            }

            // var_dump(print_r([
            //     'MemoryFile'  => $memoryfile,
            //     'destination' => $this->repositoryPath . '/' . $memoryfile['destination'] . '/' . $file->getBaseName()
            // ], true));

            $this->repository->add($memoryfile['destination'], $file);
        }
    }
}

/* End of file */
