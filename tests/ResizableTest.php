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
    public function it_saves_the_image_in_default_folder()
    {

        $image = new Image();
        $image->name = 'test';
        $image->attachMedia($this->resizableFile);
        $image->save();

        $column = $image->getResizableColumnName();

        $this->assertFileExists('tests/storage/uploads/thumb/' . $image->$column);
        $this->assertFileExists('tests/storage/uploads/mid/' . $image->$column);
        $this->assertFileExists('tests/storage/uploads/lg/' . $image->$column);
        $this->assertFileExists('tests/storage/uploads/original/' . $image->$column);

    }

    /**
     * @test
     */
    public function it_save_the_image_name_in_db()
    {

        $image = new Image();
        $image->name = 'test';
        $image->attachMedia($this->resizableFile);
        $image->save();

        $column = $image->getResizableColumnName();

        $this->seeInDatabase('images', [$column => $image->$column]);
    }

    /**
     * @test
     */
    public function it_saves_the_image_with_no_settings()
    {

        $image = new ImageNoSettings();
        $image->name = 'test';
        $image->attachMedia($this->resizableFile);
        $image->save();
        
        $column = $image->getResizableColumnName();

        $this->assertFileNotExists('tests/storage/uploads/thumb/' . $image->$column);
        $this->assertFileNotExists('tests/storage/uploads/original/' . $image->$column);
    }

    /**
     * @test
     */
    public function it_can_check_if_model_has_media()
    {

        $image = new ImageNoSettings();

        $this->assertFalse($image->hasMedia());

        $image = new ImageNoSettings();
        $image->attachMedia($this->resizableFile);

        $this->assertTrue($image->hasMedia());
    }

    /**
     * @test
     */
    public function it_can_check_if_should_keep_original()
    {
        $image = new ImageNoSettings();

        $this->assertTrue($image->shouldKeepOriginal());

        config(['resizable.keep_original' => false]);

        $image = new ImageNoSettings();

        $this->assertFalse($image->shouldKeepOriginal());

    }

    /**
     * @test
     */
    public function it_can_save_formats_in_arbitrary_folder()
    {

        $image = new Image();
        $image->name = 'test';
        $image->attachMedia($this->resizableFile)
            ->to('tests/storage/uploads/folder');
        $image->save();

        $column = $image->getResizableColumnName();

        $this->assertFileExists('tests/storage/uploads/folder/thumb/' . $image->$column);

    }
}