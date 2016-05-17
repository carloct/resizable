<?php

namespace Keisen\Resizable;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManager;

trait ResizableTrait {

    public function resize(UploadedFile $file, string $folder = null)
    {
        if ($this->hasFormats()) {
            
            $formats = $this->getFormats();
            $format= key($formats);
            $filter = key($formats[$format]);

            $manager = new ImageManager();
            $image = $manager->make($file);

            $output = $image->$filter($formats[$format][$filter][0],$formats[$format][$filter][1]);

            $filename = $this->generateName($file);
            $output->save($folder ."/". $filename );
        }
    }

    public function hasFormats() : bool
    {
        if (isset($this->resizable['formats']) &&
            !empty($this->resizable['formats'])) {
            return true;
        }

        return false;
    }
    
    public function getFormats()
    {
        if(isset($this->resizable['formats']) &&
            is_array($this->resizable['formats'])) {
            return $this->resizable['formats'];
        }

        return null;
    }

    public function generateName(UploadedFile  $file) : string
    {
        $ext = "." . $file->getClientOriginalExtension();
        $filename = preg_replace("/[^a-z0-9\\._-]+/", "", strtolower($file->getClientOriginalName()));
        return basename($filename, $ext) . "-" . str_random(6) . $ext;
    }

}