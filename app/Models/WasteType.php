<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    // WasteType.php
protected $fillable = ['waste_category_id', 'label', 'name', 'disposal_tip'];
public function category() { return $this->belongsTo(WasteCategory::class, 'waste_category_id'); }
}
