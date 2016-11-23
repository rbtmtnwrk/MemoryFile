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

    public function getRepository()
    {
        return $this->repository();
    }

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
        $filter    = new FilenameFilter($filter, '/\.(?:jpeg|jpg|tiff|mov|mp4|avi|wmv)$/');

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

            if (! $this->repository->add($memoryfile['folder'], $file)->getLastResult()) {

                $this->getLog('import')->warning('Duplicate File', [
                    'source' => $file->getPathName(),
                    'destination' => $this->repository->getLastDestination(),
                ]);

                /**
                 * @NOTE: Temporary echo until console command is in place.
                 */
                echo '.';

                continue;
            }

            /**
             * @NOTE: Temporary echo until console command is in place.
             */
            echo '+';

            $this->getLog('import')->info($this->repository->getLastDestination());
        }

        echo " Done\n";
    }

    public function transformFile($path)
    {
        $file = new \SplFileInfo($path);

        $exif = @exif_read_data($file->getPathName(), 0, true);
        $memoryfile = $this->transformer->setExif($exif)->setSplFileInfo($file)->transform();

        if ($memoryfile['mime'] == 'directory') {
            return $path . ' is a directory';
        }

        return $memoryfile;
    }
}

/* End of file */
