<?php
use MemoryFile\Service;
use MemoryFile\Testing\TestCase;

class SerivceTest extends TestCase
{
    public function test_service_create()
    {
        $dir = '/test/directory';

        $service = Service::create($dir);

        var_dump(print_r([
            'file' => __FILE__ . ' line ' . __LINE__,
            'service' => $service,
        ], true));
    }
}

/* End of file */
