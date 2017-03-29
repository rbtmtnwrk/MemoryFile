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

    /**
     * Writes md info file into the directory of the imported files.
     * Info files are hints to the contents of the folder.
     * @param  string $path
     * @param  string $info
     * @param  string $appendInfo
     * @return void
     */
    public function writeInfoFile($memoryfile, $info, $appendInfo = null)
    {
        $name = $info . '.md';
        $path = $this->repository->getRepositoryPath() . '/' . $memoryfile['folder'];

        /**
         * Write initial file if not there.
         */
        ! file_exists($path . '/' . $info . '.md') && file_put_contents($path . '/' . $name, "# $info\n", FILE_APPEND);

        /**
         * Append info
         */
        file_put_contents($path . '/' . $name, $appendInfo . "\n", FILE_APPEND);
    }

    public function import($path, $info = null)
    {
        $iterator = $this->createIterator($path);
        $regex    = $this->getRegex();
        $count    = 0;
        $added    = 0;
        $folders  = [];
        $skipped  = [];
        $destinations = [];

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

            if ($this->repository->add($memoryfile)->wasDuplicate()) {
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
            $destinations[dirname($this->repository->getDestination())] = 1;
            echo '+ ' . $file->getPathName() . ' | ' . $this->repository->getDestination() . "\n";

            /**
             * Write info file if given.
             */
            $info && $this->writeInfoFile($memoryfile, $info, $memoryfile['subFolder'] . '/' . $memoryfile['name']);

            $added++;

            $this->getLog($path)->info($this->repository->getDestination());
        }

        /**
         * Remove the initial folder as it is implied.
         */
        array_shift($folders);

        $report = [
            'Extensions'     => $this->extensions,
            'Folders Read'   => $folders,
            'Filtered Files' => $skipped,
            'Total Files'    => $count + count($skipped),
            'Added Files'    => $added,
            'Destination Paths' => array_keys($destinations),
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
            $echo .= "\n";
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
