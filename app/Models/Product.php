<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use Notifiable;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'type',
        'product_category_id',
        'enterprise_id',
        'description',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }

    public function logs()
    {
        return $this->hasMany(ProductLog::class, 'product_id')
            ->orderBy('created_at', 'desc');
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'product_image', 'product_id', 'image_id');
    }

    public function advanced()
    {
        return $this->hasOne(ProductAdvanced::class, 'product_id');
    }
}
