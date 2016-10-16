<?php
use MemoryFile\Service;
use MemoryFile\Testing\TestCase;

class SerivceTest extends TestCase
{
    public function test_service()
    {
        $dir = '/Users/rob.marton/projects/console/MemoryFile/photos';

        $service = new Service($dir);
    }
}

/* End of file */
