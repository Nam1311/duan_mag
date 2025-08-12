<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductCountDown;
use Illuminate\Support\Facades\Storage;


class Products extends Model
{
    use SoftDeletes;
    public $timestamps = false;
    protected $fillable = ['name','sku', 'price', 'slug', 'category_id', 'description', 'original_price', 'sale', 'sold_count', 'is_featured', 'is_active'];
    protected $dates = ['deleted_at'];

    // Scope để lấy sản phẩm active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope để lấy sản phẩm featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function images()
    {
        return $this->hasMany(Product_images::class, 'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(product_variants::class, 'product_id', 'id');
    }


    public function countDowns()
    {
        return $this->belongsToMany(ProductCountDown::class, 'product_count_down_pivot', 'product_id', 'count_down_id');
    }


    // cd
    public function getFinalPriceAttribute()
    {
        $now = now();

        $countdown = $this->countDowns()
            ->where('product_count_downs.status', 'active')
            ->where('product_count_downs.start_hour', '<=', $now)
            ->where('product_count_downs.end_hour', '>=', $now)
            ->first();

        $baseSale = floatval($this->sale);
        $countdownSale = $countdown ? floatval($countdown->percent_discount) : 0;

        $totalSale = min($baseSale + $countdownSale, 100);

        return round($this->original_price * (1 - $totalSale / 100), 2);
    }

    public function getTotalSaleAttribute()
    {
        $now = now();

        $countdown = $this->countDowns()
            ->where('product_count_downs.status', 'active')
            ->where('product_count_downs.start_hour', '<=', $now)
            ->where('product_count_downs.end_hour', '>=', $now)
            ->first();

        $baseSale = floatval($this->sale);
        $countdownSale = $countdown ? floatval($countdown->percent_discount) : 0;

        return min($baseSale + $countdownSale, 100);
    }


    public function category()
    {
        return $this->belongsTo(Product_categories::class, 'category_id');
    }
    public function reviews()
{
    return $this->hasMany(\App\Models\reviews::class, 'product_id', 'id');
}

public function thumbnail()
{
    return $this->hasOne(product_images::class, 'product_id', 'id')->where('order', 1);
}


    // Khi xóa sản phẩm thì xóa ảnh luôn
    protected static function booted()
    {
        static::deleting(function ($product) {
            // Xóa ảnh
            foreach ($product->images as $image) {
                $imagePath = public_path('img/products/' . $image->path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }

            // Xóa biến thể
            foreach ($product->variants as $variant) {
                $variant->delete();
            }
        });
    }

}
