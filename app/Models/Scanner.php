<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scanner extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function backgroundImageLink()
    {
        if (($this->background_img)) {
            $imagePath = $this->background_img;
            return '/storage/' . $imagePath;
        }
        else {
            return "";
        }
    }
}
