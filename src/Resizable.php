<?php

namespace Keisen\Resizable;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Resizable {

    public function resize(UploadedFile $file, string $folder);
}