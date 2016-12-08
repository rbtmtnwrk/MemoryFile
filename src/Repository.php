<?php
namespace MemoryFile;

class Repository
{
    protected $repositoryPath;
    protected $log;
    protected $lastDestination;
    protected $lastResult;
    protected $duplicate;

    use \MemoryFile\RepositoryPathTrait;

    public function wasDuplicate()
    {
        return $this->duplicate;
    }

    public function getLastDestination()
    {
        return $this->lastDestination;
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

    public function incrementFile($splFileInfo, $destination) {
        $ext      = $splFileInfo->getExtension();
        $filename = trim($splFileInfo->getBaseName($ext), '.');
        $parts    = explode('_', $filename);
        $mfx      = array_search('MF', $parts);

        if ($mfx) {
            $index = $parts[$mfx + 1];
            $parts[$mfx + 1] = $index + 1;
        } else {
            $parts[] = 'MF';
            $parts[] = '1';
        }

        $newname = implode('_', $parts) . '.' . $ext;
        $destinationParts = explode('/', $destination);
        array_pop($destinationParts);
        $destinationParts[] = $newname;

        return implode('/', $destinationParts);
    }

    public function add($folder, $splFileInfo)
    {
        $this->createPath($folder);

        $destination = $this->getRepositoryPath() . '/' . $folder . '/' . $splFileInfo->getBaseName();

        $copyable = $this->copyable($splFileInfo, $destination);

        (! $copyable['duplicate']) && copy($splFileInfo->getPathName(), $copyable['destination']);

        $this->lastDestination = $copyable['destination'];
        $this->duplicate       = $copyable['duplicate'];

        return $this;
    }

    /**
     * @TODO: Fix this
     */
    public function copyable($splFileInfo, $destination)
    {
        $copyable = ['destination' => $destination, 'duplicate' => false];

        if (file_exists($destination)) {
            $copyable['duplicate'] = (sha1_file($destination) == sha1_file($splFileInfo->getPathName()));

            if ($copyable['duplicate']) {
                return $copyable;
            } else {
                $destination = $this->incrementFile($splFileInfo, $destination);
                return $this->copyable($splFileInfo, $destination);
            }
        }

        return $copyable;
    }
}