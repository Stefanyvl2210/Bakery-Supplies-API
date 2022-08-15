<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model {
    use HasFactory;

    protected $table = "categories";

    protected $fillable = [
        'name', 'slug', 'parent_id',
    ];

    public function products() {
        return $this->belongsToMany( Product::class, 'product_categories', 'category_id', 'product_id' )->withPivot( 'quantity' );
    }

}
