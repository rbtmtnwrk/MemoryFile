<?php
use MemoryFile\Transformer;
use MemoryFile\Testing\IntegrationCase;

class TransformerTest extends IntegrationCase
{
    public function test_date()
    {
        $transformer = new Transformer;
        $path        = $this->getTestFilePath();
        $file        = new \SplFileInfo($path);

        $exif = @exif_read_data($file->getPathName(), 0, true);
        $memoryFile = $transformer->setExif($exif)->setSplFileInfo($file)->transform();

        error_log(print_r([
            'file' => __FILE__ . ' line ' . __LINE__,
            'memoryFile' => $memoryFile,
        ], true));
    }
}

/* End of file */
