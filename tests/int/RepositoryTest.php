<?php
use MemoryFile\Service;
use MemoryFile\Repository;
use MemoryFile\Testing\IntegrationCase;

class RepositoryTest extends IntegrationCase
{
    public function test_file_increment()
    {
        $repository  = new Repository;
        $path        = getcwd() . '/tests/files/RepositoryTestFile.txt';
        $file        = new \SplFileInfo($path);
        $destination = $repository->incrementFile(['splFileInfo' => $file], getcwd() . '/tests/files/RepositoryTestFile.txt');
        $expected    = getcwd() . '/tests/files/MF_1_RepositoryTestFile.txt';

        $this->assertEquals($expected, $destination);
    }

    public function test_second_file_increment()
    {
        $repository  = new Repository;
        $path        = getcwd() . '/tests/files/MF_1_RepositoryTestFile.txt';
        $file        = new \SplFileInfo($path);
        $destination = $repository->incrementFile(['splFileInfo' => $file], $path);
        $expected    = getcwd() . '/tests/files/MF_2_RepositoryTestFile.txt';

        $this->assertEquals($expected, $destination);
    }

    public function test_copyable_duplicate()
    {
        $repository  = new Repository;
        $destination = getcwd() . '/tests/files/RepositoryTestFile_MF_1.txt';
        $file        = new \SplFileInfo($destination);
        $copyable    = $repository->copyable(['splFileInfo' => $file], $destination);

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
        $copyable    = $repository->copyable(['splFileInfo' => $file], $destination);

        $expected = [
            'destination' => getcwd() . '/tests/files/RepositoryTestFile_MF_2.txt',
            'duplicate'   => false,
        ];

        $this->assertSame($expected, $copyable);
    }
}

/* End of file */
