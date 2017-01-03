<?php
use MemoryFile\Repository;
use MemoryFile\Testing\TestCase;

class RepositoryTest extends TestCase
{
    public function test_file_increment()
    {
        $repository  = new Repository;
        $path        = getcwd() . '/tests/files/RepositoryTestFile.txt';
        $file        = new \SplFileInfo($path);
        $destination = $repository->incrementFile($file, getcwd() . '/tests/files/RepositoryTestFile.txt');
        $expected    = getcwd() . '/tests/files/MF_1_RepositoryTestFile.txt';

        $this->assertEquals($expected, $destination);
    }

    public function test_second_file_increment()
    {
        $repository  = new Repository;
        $path        = getcwd() . '/tests/files/MF_1_RepositoryTestFile.txt';
        $file        = new \SplFileInfo($path);
        $destination = $repository->incrementFile($file, $path);
        $expected    = getcwd() . '/tests/files/MF_2_RepositoryTestFile.txt';

        $this->assertEquals($expected, $destination);
    }

    public function test_copyable_duplicate()
    {
        $repository  = new Repository;
        $destination = getcwd() . '/tests/files/RepositoryTestFile_MF_1.txt';
        $file        = new \SplFileInfo($destination);
        $copyable    = $repository->copyable($file, $destination);

        $expected = [
            'destination' => $destination,
            'duplicate'   => true,
        ];

        $this->assertSame($expected, $copyable);
    }

    public function test_copyable_not_duplicate()
    {
        $repository  = new Repository;
        $destination = getcwd() . '/tests/files/RepositoryTestFile_MF_1.txt';
        $file        = new \SplFileInfo(getcwd() . '/tests/files/subdirectory/RepositoryTestFile_MF_1.txt');
        $copyable    = $repository->copyable($file, $destination);

        $expected = [
            'destination' => getcwd() . '/tests/files/RepositoryTestFile_MF_2.txt',
            'duplicate'   => false,
        ];

        $this->assertSame($expected, $copyable);
    }
}

/* End of file */
