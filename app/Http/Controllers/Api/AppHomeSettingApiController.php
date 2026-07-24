<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppHomeSettingResource;
use App\Models\AppHomeSetting;

class AppHomeSettingApiController extends Controller
{
    public function show()
    {
        $settings = AppHomeSetting::firstOrCreate([], [
            'hero_title' => 'Deco Sim',
            'hero_subtitle' => 'Premium products and services',
            'hero_media_type' => 'image',
            'hero_image_fit' => 'contain',
            'theme_mode' => 'auto',
            'primary_color' => '#d6a84f',
            'secondary_color' => '#111111',
            'button_color' => '#d6a84f',
            'text_color' => '#ffffff',
            'overlay_enabled' => true,
            'overlay_color' => '#000000',
            'overlay_opacity' => 0.35,
            'banner_opacity' => 1.00,
            'hero_image_opacity' => 1.00,
            'show_popular_products' => true,
            'show_categories' => true,
            'show_coupons' => true,
        ]);

        return new AppHomeSettingResource($settings);
    }
}
