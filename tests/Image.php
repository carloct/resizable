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
        'column' => 'file',
        'keep_original' => true,
        'formats' => [
            'thumb' => ['resize'    => [100, 100]],
            'mid'   => ['resize'    => [200, 200]],
            'lg'    => ['resize'    => [300, 300]]
        ]
    ];

}