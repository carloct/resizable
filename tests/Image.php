<?php

namespace Keisen\Resizable\Test;

use Illuminate\Database\Eloquent\Model;
use Keisen\Resizable\Resizable;
use Keisen\Resizable\ResizableTrait;

class Image extends Model implements Resizable
{
    use ResizableTrait;

    public $table = 'images';
    public $guarded = [];
    public $timestamps = false;

    public $resizable = [
        'columns' => 'file',
        'formats' => [
          'thumb' => [ 'resize' => [100, 100]]
        ]
    ];

}