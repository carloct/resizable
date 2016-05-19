<?php

namespace Keisen\Resizable;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManagerStatic;
use Keisen\Resizable\Exceptions\ResizableException;

trait ResizableTrait
{

    /**
     * Resize the image to the specified formats and save the filename in
     * the object property
     *
     * @param UploadedFile $file   file
     * @param string|null  $folder folder
     *
     * @throws ResizableException
     *
     * @return void
     */
    public function resize(UploadedFile $file, string $folder = null)
    {
        if (!file_exists($folder)) {
            throw new ResizableException("The folder {$folder} doesn't exist");
        }

        if ($this->hasFormats()) {

            $formats = $this->getFormats();
            $column = $this->getColumnName();
            $this->$column = $this->generateName($file);

            foreach ($formats as $format => $filter) {
                
                $func = key($filter);
                $args = $filter[$func];

                $destFolder = $folder . "/{$format}/";

                if (!$this->createFolder($destFolder)) {
                    throw new ResizableException("Cannot create format folder");
                }

                ImageManagerStatic::make($file)
                    ->$func($args[0], $args[1])
                    ->save($destFolder . $this->$column);
            }

        }
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
    public function generateName(UploadedFile  $file) : string
    {
        $ext = "." . $file->getClientOriginalExtension();
        $filename = preg_replace(
            "/[^a-z0-9\\._-]+/",
            "",
            strtolower($file->getClientOriginalName())
        );

        return basename($filename, $ext) . "-" . str_random(6) . $ext;
    }

    /**
     * Get the column where to store the filename
     *
     * @return string
     */
    public function getColumnName() : string
    {
        if (isset($this->resizable['column'])) {
            return $this->resizable['column'];
        }

        return 'file';
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
        if (!file_exists($folder)) {
            return mkdir($folder, 0777, true);
        }

        return true;
    }

}