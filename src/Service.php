<?php
namespace MemoryFile;

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
        $iterator = new \RecursiveDirectoryIterator($path);

        return new \RecursiveIteratorIterator($iterator);
    }

    public function getRegex()
    {
        $mime = array_merge($this->transformer->getMimePhoto(), $this->transformer->getMimeMovie());
        $mime = array_flip(array_flip(array_values($mime)));
        $this->extensions  = implode('|', $mime);

        return '/\.(?:' . $this->extensions . ')$/';
    }

    public function import($path)
    {
        $iterator = $this->createIterator($path);
        $regex    = $this->getRegex();
        $count    = 0;
        $added    = 0;
        $folders  = [];
        $skipped  = [];

        echo 'Starting import for: ' . $path . "\n";

        foreach ($iterator as $file) {

            /**
             * Filter for extension and directory
             */
            if ($file->isFile()) {
                if (! preg_match($regex, strtolower($file->getFilename()))) {
                    $skipped[] = $file->getPathName();
                    continue;
                }
            } else {
                $file->getFilename() == '.' && ($folders[] = trim($file->getPathName(), '.'));

                continue;
            }

            $exif = @exif_read_data($file->getPathName(), 0, true);
            $memoryfile = $this->transformer->setExif($exif)->setSplFileInfo($file)->transform();

            $count++;

            if ($this->repository->add($memoryfile['folder'], $file)->wasDuplicate()) {
                $this->getLog($path)->warning('Duplicate File', [
                    'source'      => $file->getPathName(),
                    'destination' => $this->repository->getDestination(),
                ]);

                /**
                 * @NOTE: Temporary echo until console command is in place.
                 */
                echo 'Duplicate [' . $file->getPathName() . ' | ' . $this->repository->getDestination() . ']' . "\n";

                continue;
            }

            /**
             * @NOTE: Temporary echo until console command is in place.
             */
            echo '+ ' . $file->getPathName() . ' | ' . $this->repository->getDestination() . "\n";
            $added++;

            $this->getLog($path)->info($this->repository->getDestination());
        }

        /**
         * Remove the initial folder as it is implied.
         */
        array_shift($folders);

        $report = [
            'Total Files'    => $count + count($skipped),
            'Extensions'     => $this->extensions,
            'Filtered Files' => $skipped,
            'Folders'        => $folders,
            'Added'          => $added,
        ];

        $this->getLog($path)->info('Import Completed', $report);

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
