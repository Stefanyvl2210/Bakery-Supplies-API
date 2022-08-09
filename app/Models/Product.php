<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'image', 'price', 'quantity_avalaible', 'deleted_at'
    ];

    public function categories()
    {
      return $this->belongsToMany(ProductCategory::class, 'product_categories', 'product_id', 'category_id');
    }
}
