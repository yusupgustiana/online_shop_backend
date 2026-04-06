<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    // field tambahan untuk API
    protected $appends = [
        'image_url',
    ];

    // sembunyikan field mentah dari database
    protected $hidden = [
        'image',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return asset($this->image);
    }
}