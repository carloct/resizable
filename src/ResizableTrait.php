<?php

namespace Keisen\Resizable;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManager;

trait ResizableTrait
{

    /**
     * Resize the image to the specified formats and save the filename in
     * the object property
     *
     * @param UploadedFile $file   file
     * @param string|null  $folder folder
     *
     * @return void
     */
    public function resize(UploadedFile $file, string $folder = null)
    {
        if ($this->hasFormats()) {
            
            $formats = $this->getFormats();
            $format= key($formats);
            $filter = key($formats[$format]);

            $manager = new ImageManager();
            $image = $manager->make($file);

            $output = $image->$filter(
                $formats[$format][$filter][0],
                $formats[$format][$filter][1]
            );

            $column = $this->getColumnName();
            $this->$column = $this->generateName($file);

            $output->save($folder ."/". $this->$column);
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
    public function getColumnName()
    {
        if (isset($this->resizable['column'])) {
            return $this->resizable['column'];
        }

        return 'file';
    }

}