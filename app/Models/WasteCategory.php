<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteCategory extends Model
{
        // WasteCategory.php
protected $fillable = ['name', 'description'];
public function wasteTypes() { return $this->hasMany(WasteType::class); }
    }
