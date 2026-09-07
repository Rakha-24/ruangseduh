<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['image', 'caption'])]
class Gallery extends Model
{
    use HasImage;

    protected $table = 'gallery';

    //
}
