<?php
namespace MemoryFile;

use MemoryFile\FileSystem\DirnameFilter;
use MemoryFile\FileSystem\FilenameFilter;

class Service
{
    protected $filter;
    protected $repositoryPath;
    protected $transformer;
    protected $extensions;
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

        $this->filter = new DirnameFilter($directory, '/^(?!\.Trash)/');

        $mime = array_merge($this->transformer->getMimePhoto(), $this->transformer->getMimeMovie());
        $mime = array_flip(array_flip(array_values($mime)));
        $this->extensions  = implode('|', $mime);

        $this->filter = new FilenameFilter($this->filter, '/\.(?:' . $this->extensions . ')$/');

        return new \RecursiveIteratorIterator($this->filter);
    }

    public function import($path)
    {
        $iterator = $this->createIterator($path);
        $count    = 0;
        $added    = 0;

        foreach ($iterator as $file) {
            $exif = @exif_read_data($file->getPathName(), 0, true);
            $memoryfile = $this->transformer->setExif($exif)->setSplFileInfo($file)->transform();

            if ($memoryfile['mime'] == 'directory') {
                continue;
            }

            if (! $this->repository->add($memoryfile['folder'], $file)->getLastResult()) {

                $count++;

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
            $added++;

            $this->getLog('import')->info($this->repository->getLastDestination());
        }

        $report = [
            'Total Files and Directories' => $count + count($this->filter->getFiltered()) + count($this->filter->getFolders()),
            'Extensions'     => $this->extensions,
            'Filtered Files' => $this->filter->getFiltered(),
            'Folders'        => $this->filter->getFolders(),
            'Added'          => $added,

        ];

        $this->getLog('import')->info('Import Completed', $report);

        $echo = "\nImport Completed\n";
        foreach ($report as $key => $value) {
            if (is_array($value)) {
                $valueOutput = '';
                foreach($value as $val) {
                    $valueOutput .= "- $val\n";
                }

                $echo .= "$key:\n$valueOutput";
            } else {
                $echo .= "$key:  $value\n";
            }

        }

        echo $echo;
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
