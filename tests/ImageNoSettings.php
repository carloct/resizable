<?php

namespace Keisen\Resizable\Test;

use Illuminate\Database\Eloquent\Model;
use Keisen\Resizable\Resizable;
use Keisen\Resizable\ResizableTrait;

class ImageNoSettings extends Model implements Resizable
{
    use ResizableTrait;

    public $table = 'images';
    public $guarded = [];
    public $timestamps = false;

}