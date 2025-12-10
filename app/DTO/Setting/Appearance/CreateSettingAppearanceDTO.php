<?php

namespace App\DTO\Setting\Appearance;

use App\DTO\BaseDTO;

class CreateSettingAppearanceDTO extends BaseDTO
{
    public function __construct(
        public readonly int $enterprise_id,
        public readonly int $title_page_color_default,
        public readonly int $navbar_color_default,
        public readonly int $navbar_icon_color_default,
        public readonly int $side_menu_color_default_not_selected_item,
        public readonly int $side_menu_color_default_selected_item,
        public readonly int $side_menu_color_default_not_selected_icon,
        public readonly int $side_menu_color_default_selected_icon,
        public readonly ?string $title_page_color_code,
        public readonly ?string $navbar_color_code,
        public readonly ?string $navbar_icon_color_code,
        public readonly ?string $side_menu_color_code_not_selected_item,
        public readonly ?string $side_menu_color_code_selected_item,
        public readonly ?string $side_menu_color_code_not_selected_icon,
        public readonly ?string $side_menu_color_code_selected_icon
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            enterprise_id: $data['enterpriseID'],
            title_page_color_default: 1,
            navbar_color_default: 1,
            navbar_icon_color_default: 1,
            side_menu_color_default_not_selected_item: 1,
            side_menu_color_default_selected_item: 1,
            side_menu_color_default_not_selected_icon: 1,
            side_menu_color_default_selected_icon: 1,
            title_page_color_code: null,
            navbar_color_code: null,
            navbar_icon_color_code: null,
            side_menu_color_code_not_selected_item: null,
            side_menu_color_code_selected_item: null,
            side_menu_color_code_not_selected_icon: null,
            side_menu_color_code_selected_icon: null,
        );
    }
}
