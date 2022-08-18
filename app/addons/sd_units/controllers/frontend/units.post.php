<?php

use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * @var string $mode
 * @var string $action
 */


if ($mode == 'units'){    
    $_REQUEST['unit_id'] = empty($_REQUEST['unit_id']) ? 0 : $_REQUEST['unit_id'];

    Tygh::$app['session']['continue_url'] = "units.units";

    $unit_data = fn_get_unit_data($_REQUEST['unit_id'], CART_LANGUAGE, '*', true, false, $preview);
    $params = $_REQUEST;   
    if ($items_per_page = fn_change_session_param(Tygh::$app['session'], $_REQUEST, 'items_per_page')) {
        $params['items_per_page'] = $items_per_page;        
    }
   
    list($units, $search) = fn_get_units($params, Registry::get('settings.Appearance.products_per_page'), CART_LANGUAGE);

    if (isset($search['page']) && ($search['page'] > 1) && empty($units)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
   $selected_layout = fn_get_products_layout($_REQUEST);

   fn_filters_handle_search_result($params, $units, $search);

   $selected_layout = fn_get_products_layout($_REQUEST);

    
    Tygh::$app['view']->assign('units', $units);
    Tygh::$app['view']->assign('search', $search);          
    Tygh::$app['view']->assign('columns',3);   
    Tygh::$app['view']->assign('selected_layout',$selected_layout);   
    Tygh::$app['view']->assign('unit_data', $unit_data);   
    
    if(!empty($unit_data['page_title'])){
        Tygh::$app['view']->assign('page_title', $unit_data['page_title']);  
    }
    // [Breadcrumbs]
    fn_add_breadcrumb("Отделы");
} 
if ($mode === 'unit') 
{
    
    $unit_data = [];
    $unit_head_data = [];
    $unit_id = !empty($_REQUEST['unit_id']) ? $_REQUEST['unit_id'] : 0;
    $unit_data = fn_get_slave_data($unit_id, CART_LANGUAGE);
    $unit_head_data = fn_get_unit_data($unit_id, CART_LANGUAGE);
    
    //fn_print_die($unit_head_data);
    Tygh::$app['view']->assign('user_data', $unit_data);
    Tygh::$app['view']->assign('unit_head_data', $unit_head_data);
    
    fn_add_breadcrumb("Отделы", $unit_data['unit']);
    
    $params = $_REQUEST;
    $params['extend'] = array('units', 'description');
    $params['items_ids'] = !empty($unit_data['users']) ? implode (',', $unit_data['users']) : -1;

}
$boss_info = fn_get_user_short_info($unit_data['user_id']);
$workers_info = db_get_fields("SELECT user_id 
                FROM ?:users 
                WHERE user_id IN(?n) ",
                explode ('.', $unit_data['slave_id']));

list($workers_info, $search) = fn_get_users($params, Registry::get('settings.Appearance.products_elements_per_page'), CART_LANGUAGE);

Tygh::$app['view']->assign('boss_info', $boss_info);
Tygh::$app['view']->assign('search', $search);
Tygh::$app['view']->assign('workers_info', $workers_info);
