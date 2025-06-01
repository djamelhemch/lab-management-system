<?php
// app/Models/Analysis.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    protected $fillable = [
        'code', 'name', 'category', 'unit', 'sex_applicable',
        'age_min', 'age_max', 'pregnant_applicable', 'sample_type',
        'normal_min', 'normal_max', 'formula', 'price', 'is_active'
    ];

    protected $casts = [
        'pregnant_applicable' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'normal_min' => 'float',
        'normal_max' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}