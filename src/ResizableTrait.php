<?php

namespace Keisen\Resizable;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManagerStatic;
use Keisen\Resizable\Exceptions\ResizableException;
use Belt\Folder;
use Belt\Filename;

trait ResizableTrait
{

    protected $resizable_media;
    protected $resizable_folder;

    /**
     * Attach the uploaded file to the model
     *
     * @param UploadedFile $media
     *
     * @return Model
     */
    public function attachMedia(UploadedFile $media) : Model
    {
        $this->resizable_media = $media;

        $column = $this->getResizableColumnName();
        $this->$column = $this->generateFileName($this->resizable_media);

        return $this;
    }

    /**
     * Specify the destination folder
     *
     * @param string $folder
     *
     * @return Model
     */
    public function to(string $folder) : Model
    {
        $this->resizable_folder = $folder;

        return $this;
    }

    /**
     * Resize the image to the specified formats and save the filename in
     * the object property
     *
     * @throws ResizableException
     *
     * @return void
     */
    public function resize()
    {
        if (empty($this->resizable_media)) {
            throw new ResizableException('No media attached to the model');
        }

        if ($this->hasFormats()) {

            Folder::create($this->getDestinationFolder());

            $formats = $this->getFormats();
            $column = $this->getResizableColumnName();
            $folder = $this->getDestinationFolder();

            foreach ($formats as $format => $filter) {

                $func = key($filter);
                $args = $filter[$func];

                $formatFolder = $folder . "/{$format}/";

                $this->createFolder($formatFolder);

                ImageManagerStatic::make($this->resizable_media)
                    ->$func($args[0], $args[1])
                    ->save($formatFolder . $this->$column);
            }

            if ($this->shouldKeepOriginal()) {
                $this->resizable_media->move($folder . "/original/", $this->$column);
            }
        }
        
    }

    /**
     * Try to get the destination folder from the model property
     * or fallback to the config parameter
     *
     * @return string
     */
    public function getDestinationFolder() : string
    {
        if (isset($this->resizable_folder)
            && !empty($this->resizable_media)
        ) {
            return $this->resizable_folder;
        }

        if (isset($this->resizable['folder'])) {
            return $this->resizable['folder'];
        }

        return config('resizable.folder', 'storage/uploads');
    }

    /**
     * Check if there are formats specified, otherwise fallback
     *
     * @return bool
     */
    public function hasFormats() : bool
    {
        if (isset($this->resizable['formats'])
            && !empty($this->resizable['formats'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get the formats from the object property
     *
     * @return null
     */
    public function getFormats()
    {
        if (isset($this->resizable['formats'])
            && is_array($this->resizable['formats'])
        ) {
            return $this->resizable['formats'];
        }

        return null;
    }

    /**
     * Generate a pseudo-unique filename
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    public function generateFileName(UploadedFile  $file) : string
    {

        if ($this->shouldAddEntropy()) {
            return Filename::appendEntropy($file->getClientOriginalName(), 8);
        }

        return $file->getClientOriginalName();

    }

    /**
     * Get the column where to store the filename
     *
     * @return string
     */
    public function getResizableColumnName() : string
    {
        if (isset($this->resizable['column'])) {
            return $this->resizable['column'];
        }

        return config('resizable.column');
    }

    /**
     * Create folder if doesn't exists
     *
     * @param string $folder
     *
     * @return bool
     */
    public function createFolder($folder) : bool
    {
        return Folder::create($folder);
    }

    /**
     * Should keep the original
     *
     * @return bool
     */
    public function shouldKeepOriginal() : bool
    {
        if (isset($this->resizable['keep_original'])) {
            return $this->resizable['keep_original'];
        }

        return config('resizable.keep_original', true);
    }

    /**
     * Should add entropy to filenames
     *
     * @return bool
     */
    public function shouldAddEntropy() : bool
    {
        if (isset($this->resizable['entropy'])) {
            return $this->resizable['entropy'];
        }

        return config('resizable.entropy', true);
    }

    /**
     * Check if the model has a file attached
     *
     * @return bool
     */
    public function hasMedia() : bool
    {
        if (!empty($this->resizable_media)
            && $this->resizable_media instanceof UploadedFile
        ) {
            return true;
        }

        return false;
    }

}