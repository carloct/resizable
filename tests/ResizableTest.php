<?php

namespace Keisen\Resizable\Test;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResizableTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_saves_the_image_in_folder()
    {
        $file = new UploadedFile(
            'tests/fixtures/test.jpg',
            'test.jpg',
            null,
            null,
            null,
            true
        );

        $image = new Image();
        $image->name = 'test';
        $image->resize($file, 'tests/storage');

        $column = $image->getColumnName();

        $this->assertFileExists('tests/storage/' . $image->$column);
    }

    /**
     * @test
     */
    public function it_save_the_image_name_in_db()
    {
        $file = new UploadedFile(
            'tests/fixtures/test.jpg',
            'test.jpg',
            null,
            null,
            null,
            true
        );

        $image = new Image();
        $image->name = 'test';
        $image->resize($file, 'tests/storage');
        $image->save();

        $column = $image->getColumnName();

        $this->seeInDatabase('images', [$column => $image->$column]);
    }
}