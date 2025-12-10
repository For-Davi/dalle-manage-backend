<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $product = $this->findById($id);

        if ($product) {
            DB::table('product_variants')->where('product_id', $id)->delete();
            DB::table('product_log')->where('product_id', $id)->delete();
            DB::table('product_tag')->where('product_id', $id)->delete();
            DB::table('product_advanced')->where('product_id', $id)->delete();

            // Processo de exclusão de imagens
            $imageRecords = DB::table('product_image')
                ->where('product_id', $id)
                ->join('images', 'product_image.image_id', '=', 'images.id')
                ->select('images.id', 'images.url')
                ->get();
            if ($imageRecords->isNotEmpty()) {
                DB::table('product_image')->where('product_id', $id)->delete();

                if (env('APP_ENV') === 'local') {
                    foreach ($imageRecords as $image) {
                        $filePath = public_path($image->url);

                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }
                $imageIds = $imageRecords->pluck('id')->toArray();
                DB::table('images')->whereIn('id', $imageIds)->delete();
            }

            DB::table('products')->where('id', $id)->delete();

            return true;
        }

        return false;
    }
}
