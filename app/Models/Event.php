<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'image'
    ];

    public static function filterEvent($filter){
        if($filter === 'FINISHED'){
            return parent::where('end_date', '<', now());
        }
        if($filter === 'UPCOMING'){
            return parent::where('start_date', '>', now());
        }
        if($filter === 'UPCOMING7'){
            return parent::whereBetween('start_date', [now(), now()->addDays(7)]);
        }
        if($filter === 'FINISHED7'){
            return parent::whereBetween('end_date', [now()->subDays(7), now()]);
        }
        return self::query();
    }
}
