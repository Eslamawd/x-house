<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',  
        'images',
        'price',
        'old_price',
        'rating',
        'reviews',
        'description',
    ];

    protected $casts = [
        'price' => 'float',
        'old_price' => 'float',
        'images' => 'array',
        'rating' => 'float',
        'reviews' => 'integer',
       
    ];

    /**
     * العلاقة مع التصنيف (الفئة).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope لتصفية المنتجات الظاهرة فقط.
     */
    // app/Models/Product.php

  
public function getImagesAttribute($value)
{
    $images = json_decode($value, true);

    if (!$images || !is_array($images)) return [];

    return array_map(function ($image) {
        if (str_starts_with($image, 'http')) {
            return $image;
        }

        return asset('storage/' . $image); // الناتج = http://yourdomain.com/storage/products/image.png
    }, $images);
}



}
