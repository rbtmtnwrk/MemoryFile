<?php
use MemoryFile\Transformer;
use MemoryFile\Testing\TestCase;

class TransformerTest extends TestCase
{
    public function test_mime_types()
    {
        $transformer = new Transformer;

        var_dump(print_r([
            'getMimePhoto' => $transformer->getMimePhoto(),
            'getMimeMovie' => $transformer->getMimeMovie(),
        ], true));

        $this->assertEquals(true, is_array($transformer->getMimePhoto()));
        $this->assertEquals(true, is_array($transformer->getMimeMovie()));
        $this->assertEquals('photo', $transformer->type('image/jpeg'));
        $this->assertEquals('movie', $transformer->type('video/quicktime'));
    }
}

/* End of file */
