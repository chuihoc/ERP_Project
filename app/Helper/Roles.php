<?php 
namespace App\Helper;
class Roles {
  static function roleAndRouter() {
    //Cần phân tích để clear phase 2
    return [
      'quy_trinh_sx' => [
        'view' => 'quy_trinh_sx__quy_trinh_san_xuat__view',
        'add' => 'quy_trinh_sx__quy_trinh_san_xuat__add',
        'edit' => 'quy_trinh_sx__quy_trinh_san_xuat__edit',
        'delete' => 'quy_trinh_sx__quy_trinh_san_xuat__delete',
      ],
      'qlsx' => [
        'view' => 'qlsx__ans_sanxuat__view',
        'add' => 'qlsx__ans_sanxuat__add',
        'edit' => 'qlsx__ans_sanxuat__edit',
        'delete' => 'qlsx__ans_sanxuat__delete',
      ],
      'rnd' => [
        'view'    => 'rnd__view',
        'add'     => 'rnd__add',
        'edit'    => 'rnd__edit',
        'delete'  => 'rnd__delete',
      ],
      'khsx_daihan' => [
        'view' => 'khsx_daihan__view'
      ],
      'tinhtrangkho' => [
        'view' => 'tinhtrangkho__view'
      ],
      'qlphieunhap' => [
        'view' => 'qlphieunhap__phieu_nhap_xuat_kho__view',
        'add' => 'qlphieunhap__phieu_nhap_xuat_kho__add',
        'edit' => 'qlphieunhap__phieu_nhap_xuat_kho__edit',
        'delete' => 'qlphieunhap__phieu_nhap_xuat_kho__delete',
      ],
      'qlphieuxuat' => [
        'view' => 'qlphieuxuat__phieu_nhap_xuat_kho__view',
        'add' => 'qlphieuxuat__phieu_nhap_xuat_kho__add',
        'edit' => 'qlphieuxuat__phieu_nhap_xuat_kho__edit',
        'delete' => 'qlphieuxuat__phieu_nhap_xuat_kho__delete',
      ],
      'qlkho' => [
        'view' => 'qlkho__ans_kho__view',
        'add' => 'qlkho__ans_kho__add',
        'edit' => 'qlkho__ans_kho__edit',
        'delete' => 'qlkho__ans_kho__delete',
      ],
      'pheduyet_khsx' => [
        'duyet' => 'duyet_khsx'
      ],
      'qlcate' => [
        'view' => 'qlcate__view',
        'add' => 'qlcate__add',
        'edit' => 'qlcate__edit',
        'delete' => 'qlcate__delete',
      ],
      'khvt' => [
        'view' => 'khvt__view',
        'add' => 'khvt__add',
        'edit' => 'khvt__edit',
        'delete' => 'khvt__delete',
      ],
      'qldh' => [
        'view' => 'qldh__view',
        'add' => 'qldh__add',
        'edit' => 'qldh__edit',
        'delete' => 'qldh__delete',
      ],
      // 'npp' => [
      //   'view' => 'npp__view',
      //   'add' => 'npp__add',
      //   'edit' => 'npp__edit',
      //   'delete' => 'npp__delete',
      // ],
      'qluser' => [
        'view'    => 'qluser__view',
        'add'     => 'qluser__add',
        'edit'    => 'qluser__edit',
        'delete'  => 'qluser__delete',
      ],
      'qlkh' => [
        'view'    => 'qlkh__view',
        'add'     => 'qlkh__add',
        'edit'    => 'qlkh__edit',
        'delete'  => 'qlkh__delete',
      ],
      'product' => [
        'view'    => 'product__view',
        'add'     => 'product__add',
        'edit'    => 'product__edit',
        'delete'  => 'product__delete',
      ],
      'vattu' => [
        'view'    => 'vattu__view',
        'add'     => 'vattu__add',
        'edit'    => 'vattu__edit',
        'delete'  => 'vattu__delete',
      ],
      'qlns' => [
        'view'    => 'qlns__view',
        'add'     => 'qlns__add',
        'edit'    => 'qlns__edit',
        'delete'  => 'qlns__delete',
      ],
      'qljobs' => [
        'view'    => 'qljobs__view',
        'add'     => 'qljobs__add',
        'edit'    => 'qljobs__edit',
        'delete'  => 'qljobs__delete',
      ],
      'qlsl' => [
        'view'    => 'qlsl__view',
        'add'     => 'qlsl__add',
        'edit'    => 'qlsl__edit',
        'delete'  => 'qlsl__delete',
      ],
      'cdc' => [
        'view'    => 'cdc__view',
        'add'     => 'cdc__add',
        'edit'    => 'cdc__edit',
        'delete'  => 'cdc__delete',
      ],
      'qlpb' => [
        'view'    => 'qlpb__view',
        'add'     => 'qlpb__add',
        'edit'    => 'qlpb__edit',
        'delete'  => 'qlpb__delete',
      ],
      'qlvtkho' => [
        'view'    => 'qlvtkho__view',
        'add'     => 'qlvtkho__add',
        'edit'    => 'qlvtkho__edit',
        'delete'  => 'qlvtkho__delete',
      ],
      'lang' => [
        'view'    => 'lang__view',
        'add'     => 'lang__add',
        'edit'    => 'lang__edit',
        'delete'  => 'lang__delete',
      ],
      'note' => [
        'view'    => 'note__view',
        'add'     => 'note__add',
        'edit'    => 'note__edit',
        'delete'  => 'note__delete',
      ],
      'options' => [
        'view'    => 'options__view',
        'add'     => 'options__add',
        'edit'    => 'options__edit',
        'delete'  => 'options__delete',
      ],
      'job_report' => [
        'view'    => 'job_report__view',
        'add'     => 'job_report__add',
        'edit'    => 'job_report__edit',
        'delete'  => 'job_report__delete',
      ],
      'project' => [
          'view'    => 'project__view',
          'add'     => 'project__add',
          'edit'    => 'project__edit',
          'delete'  => 'project__delete',
      ],
      'sale_report_group' => [
        'view'    => 'sale_report__view',
      ],
      'sale_report/year' => [
        'view'    => 'sale_report/year__view',
      ],
      'sale_report/area' => [
        'view'    => 'sale_report/area__view',
      ],
      'sale_report/province_area' => [
        'view'    => 'sale_report/province_area__view',
      ],
      'sale_report/province' => [
        'view'    => 'sale_report/province__view',
      ],
      'sale_report/district' => [
        'view'    => 'sale_report/district__view',
      ],
      'sale_report/store' => [
        'view'    => 'sale_report/store__view',
      ], 
      'sale_report/store_product' => [
        'view'    => 'sale_report/store_product__view',
      ],
      'sale_report/seller' => [
        'view'    => 'sale_report/seller__view',
      ],
      'kpi_tdv_week' => [
        'view'    => 'kpi_tdv_week__view',
      ],
      'kpi_tdv_month' => [
        'view'    => 'kpi_tdv_month__view',
      ],
      'target_month' => [
        'view'    => 'target_monthh__view',
      ],
      'target_week' => [
        'view'    => 'target_week__view',
      ],
      'stores_status' => [
        'view'    => 'stores_status__view',
      ],
      'duocpham/orders' => [
        'view'    => 'duocpham/orders__view',
      ],
      'duocpham/product' => [
        'view'    => 'duocpham/product__view',
      ],
      'exchange' => [
        'view'    => 'exchange__view',
      ],      
      'general_group' => [
        'view'    => 'general_group__view',
      ],
      'manage_product' => [
        'view'    => 'manage_product__view',
      ],
      'manage_tdvs' => [
        'view'    => 'manage_tdv__view',
      ],
      'stores' => [
        'view'    => 'manage_store__view',
      ],
      'target_group' => [
        'view'    => 'manage_target__view',
      ],
      'sale_report/store_delivery' => [
        'view'    => 'sale_report/store_delivery__view',
      ],
      'nha_cung_cap' => [
        'view'    => 'nha_cung_cap__view',
        'add'     => 'nha_cung_cap__add',
        'edit'    => 'nha_cung_cap__edit',
        'delete'  => 'nha_cung_cap__delete',
      ],
      'request_order' => [
        'view'    => 'request_order__view',
        'add'     => 'request_order__add',
        'edit'    => 'request_order__edit',
        'delete'  => 'request_order__delete',
      ],
    ];
  }
  static function getRoles() {
    // Parents: main_group, vattu_group, qlsx_group, chamcong_group, qluser_group, other_group
    // Cần phân tích để clear phase 2. Chưa có group cha con và tự động linh hoạt theo chuẩn mô hình 
    return [
      'qluser' => [
        'label' => 'Thành viên', 
        'icon' => 'user',
        'path' => 'qluser',
        'parent' => 'qluser_group',
        'permission' => Roles::roleAndRouter()['qluser']
      ],
      [
        'label' => 'Phòng ban', 
        'icon' => 'team',
        'path' => 'qlpb',
        'parent' => 'qluser_group',
        'permission' => Roles::roleAndRouter()['qlpb']
      ],
      'lang' => [
        'label' => 'Ngôn ngữ', 
        'icon' => 'schedule',
        'path' => 'lang',
        'parent' => 'other_group',
        'permission' => Roles::roleAndRouter()['lang']
      ],
      'options' => [
        'label' => 'Cấu hình', 
        'icon' => 'tool',
        'path' => 'options',
        'parent' => 'other_group',
        'permission' => Roles::roleAndRouter()['options']
      ],
      [
        'label' => 'DT theo năm',
        'icon' => 'api',
        'path' => 'sale_report/year',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/year']
      ],
      [
        'label' => 'DT theo miền',
        'icon' => 'api',
        'path' => 'sale_report/area',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/area']
      ],
      [
        'label' => 'DT theo tỉnh miền',
        'icon' => 'api',
        'path' => 'sale_report/province_area',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/province_area']
      ],
      [
        'label' => 'DT theo tỉnh',
        'icon' => 'api',
        'path' => 'sale_report/province',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/province']
      ],
      [
        'label' => 'DT theo quận, huyện',
        'icon' => 'api',
        'path' => 'sale_report/district',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/district']
      ],
      [
        'label' => 'DT theo NT',
        'icon' => 'api',
        'path' => 'sale_report/store',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/store']
      ],
      [
        'label' => 'NT theo SP',
        'icon' => 'api',
        'path' => 'sale_report/store_product',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/store_product']
      ],
      [
        'label' => 'NT theo NPP',
        'icon' => 'api',
        'path' => 'sale_report/store_delivery',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/store_delivery']
      ],
      [
        'label' => 'DT theo TDV',
        'icon' => 'api',
        'path' => 'sale_report/seller',
        'parent' => 'sale_report_group',
        'permission' => Roles::roleAndRouter()['sale_report/seller']
      ],
      [
        'label' => 'QL Nhà thuốc',
        'icon' => 'api',
        'path' => 'stores',
        'parent' => 'store_group',
        'permission' => Roles::roleAndRouter()['stores']
      ],
      [
        'label' => 'Tình trạng NT',
        'icon' => 'api',
        'path' => 'stores_status',
        'parent' => 'stores',
        'permission' => Roles::roleAndRouter()['stores_status']
      ],
      [
        'label' => 'QL Đơn hàng',
        'icon' => 'api',
        'path' => 'duocpham/orders',
        'parent' => 'general_group',
        'permission' => Roles::roleAndRouter()['duocpham/orders']
      ],
      [
        'label' => 'QL Sản phẩm',
        'icon' => 'api',
        'path' => 'duocpham/product',
        'parent' => 'general_group',
        'permission' => Roles::roleAndRouter()['duocpham/product']
      ],
      [
        'label' => 'QL NPP',
        'icon' => 'api',
        'path' => 'agency',
        'parent' => 'general_group',
        'permission' => Roles::roleAndRouter()['general_group']
      ],
      [
        'label' => 'QL DVT',
        'icon' => 'api',
        'path' => 'exchange',
        'parent' => 'general_group',
        'permission' => Roles::roleAndRouter()['exchange']
      ],
      [
        'label' => 'QL TDV',
        'icon' => 'api',
        'path' => 'manage_tdvs',
        'parent' => 'tdv_group',
        'permission' => Roles::roleAndRouter()['manage_tdvs']
      ],
      [
        'label' => 'KPI TDV theo tuần',
        'icon' => 'api',
        'path' => 'kpi_tdv_week',
        'parent' => 'tdv_group',
        'permission' => Roles::roleAndRouter()['kpi_tdv_week']
      ],
      [
        'label' => 'KPI TDV theo tháng',
        'icon' => 'api',
        'path' => 'kpi_tdv_month',
        'parent' => 'tdv_group',
        'permission' => Roles::roleAndRouter()['kpi_tdv_month']
      ],
      [
        'label' => 'Kế hoạch tháng',
        'icon' => 'api',
        'path' => 'target_month',
        'parent' => 'target_group',
        'permission' => Roles::roleAndRouter()['target_month']
      ],
      [
        'label' => 'Kế hoạch tuần',
        'icon' => 'api',
        'path' => 'target_week',
        'parent' => 'target_group',
        'permission' => Roles::roleAndRouter()['target_week']
      ],
      [
        'label' => 'Tình trạng kho', 
        //'icon' => 'home',
        'path' => 'tinhtrangkho',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['tinhtrangkho']
      ],
      [
        'label' => 'Phiếu Nhập', 
        'icon' => 'schedule',
        'path' => 'qlphieunhap',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['qlphieunhap']
      ],
      [
        'label' => 'Phiếu Xuất', 
        'icon' => 'schedule',
        'path' => 'qlphieuxuat',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['qlphieuxuat']
      ],
      [
        'label' => 'Danh mục VT', 
        'icon' => 'table',
        'path' => 'qlcate',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['qlcate']
      ],
      'vattu' => [
        'label' => 'Vật tư', 
        'icon' => 'shop',
        'path' => 'vattu',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['vattu']
      ],
      'qlvtkho' => [
        'label' => 'Vị trí Kho', 
        'icon' => 'home',
        'path' => 'qlvtkho',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['qlvtkho']
      ],
      'khvt' => [
        'label' => 'Kế hoạch VT', 
        'icon' => 'schedule',
        'path' => 'khvt',
        'parent' => 'vattu_group',
        'permission' => Roles::roleAndRouter()['khvt']
      ],
      [
        'label' => 'Kho VT', 
        'icon' => 'home',
        'path' => 'qlkho',
        'parent' => 'vattu_group',
        'limit_view' => 'lotus_kho',
        'permission' => Roles::roleAndRouter()['qlkho']
      ],
      [
        'label' => 'Đơn đặt hàng', 
        'icon' => 'shopping-cart',
        'path' => 'request_order',
        'parent' => 'khachhang_group',
        'permission' => Roles::roleAndRouter()['request_order']
      ],
      [
        'label' => 'Nhà cung cấp', 
        'icon' => 'solution',
        'path' => 'nha_cung_cap',
        'parent' => 'khachhang_group',
        'permission' => Roles::roleAndRouter()['nha_cung_cap']
      ],
      [
        'label' => 'Khách Hàng', 
        'icon' => 'team',
        'path' => 'qlkh',
        'parent' => 'khachhang_group',
        'permission' => Roles::roleAndRouter()['qlkh']
      ],
      [
        'label' => 'Lệnh Sản Xuất', 
        //'icon' => 'inbox',
        'path' => 'qlsx',
        'parent' => 'qlsx_group',
        'permission' => Roles::roleAndRouter()['qlsx']
      ],
      [
        'label' => 'NC định mức', 
        //'icon' => 'solution',
        'path' => 'rnd',
        'parent' => 'qlsx_group',
        'permission' => Roles::roleAndRouter()['rnd']
      ],
      [
        'label' => 'Quy trình SX', 
        //'icon' => 'solution',
        'path' => 'quy_trinh_sx',
        'parent' => 'qlsx_group',
        'permission' => Roles::roleAndRouter()['quy_trinh_sx']
      ],
      [
        'label' => 'KHSX dài hạn', 
        //'icon' => 'solution',
        'path' => 'khsx_daihan',
        'parent' => 'qlsx_group',
        'permission' => Roles::roleAndRouter()['khsx_daihan']
      ],
    ];
  }
}