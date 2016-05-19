<?php

namespace Keisen\Resizable\Test;

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

        $this->assertFileExists('tests/storage/thumb/' . $image->$column);
        $this->assertFileExists('tests/storage/mid/' . $image->$column);
        $this->assertFileExists('tests/storage/lg/' . $image->$column);
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

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_folder_doesnt_exists()
    {
        $this->setExpectedException(
            '\Keisen\Resizable\Exceptions\ResizableException'
        );

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
        $image->resize($file, 'tests/bla');

    }
}