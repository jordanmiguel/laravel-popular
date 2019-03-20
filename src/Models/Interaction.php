<?php

namespace JordanMiguel\LaravelPopular\Models;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    protected $fillable = ['visitor', 'date', 'interactionable_id', 'interactionable_type', 'category'];
}
