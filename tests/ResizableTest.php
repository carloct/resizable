<?php

namespace Keisen\Resizable\Test;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ResizableTest extends TestCase
{

    /**
     * @test
     */
    public function it_save_the_image_in_default_folder()
    {
        $file = new UploadedFile('tests/fixtures/test.jpg', 'test.jpg', null, null, null, true );

        $image = new Image();
        $image->name = 'test';
        $image->resize($file, 'tests/storage');
        
        


    }
}