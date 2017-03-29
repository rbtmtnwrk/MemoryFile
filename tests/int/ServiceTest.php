<?php
use MemoryFile\Service;
use MemoryFile\Testing\IntegrationCase;

class SerivceTest extends IntegrationCase
{
    public function test_service_create()
    {
        $service = Service::create($this->getRepositoryDir());

        $this->assertEquals(true, !!$service);
    }

    public function test_info_file()
    {
        $service = Service::create($this->getRepositoryDir());

        $memoryfile = [
            'folder'    => 'month',
            'subFolder' => 'camera',
        ];

        $service->writeInfoFile($memoryfile, 'Fun Day');
        $filepath = $this->getRepositoryDir() . '/month/Fun Day.md';

        $this->assertEquals(true, file_exists($filepath));

        unlink($filepath);
    }
}

/* End of file */
