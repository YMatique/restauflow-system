<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Subcategory extends Model
{

    // Mutators & Accessors
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = Str::slug($subcategory->name);
            }
        });

        static::updating(function ($subcategory) {
            if ($subcategory->isDirty('name')) {
                $subcategory->slug = Str::slug($subcategory->name);
            }
        });
    }

    //Relationships
    public function category(){
        return $this->hasOne(Category::class);
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
