<?php
namespace MemoryFile\Testing;

use MemoryFile\Service;
use MemoryFile\Testing\TestCase;

class IntegrationCase extends TestCase
{
    public function getTestFilePath()
    {
        return '/Users/rob.marton/projects/console/MemoryFile/photos/folder_1/IMG_0001.JPG';
    }

    public function getRepositoryDir()
    {
        return getcwd() . '/tests/repository';
    }
}

/* End of file */