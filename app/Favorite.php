<?php

namespace App;

use App\Package;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id',
        'user_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
