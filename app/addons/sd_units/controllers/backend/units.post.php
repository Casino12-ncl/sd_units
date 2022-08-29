
<?php


use Tygh\Registry;
use Tygh\Tygh;


defined('BOOTSTRAP') or die('Access denied');

$auth = & Tygh::$app['session']['auth'];
$suffix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $suffix = '';
    fn_trusted_vars(
        'unit_data',
        'update_unit',
        'unit_id',
        'manage_units',
        'user_id',
        'user_info',
        'unit_ids',
        'boss_info',
        'worker_info',
        'firstname',
        'users'
    );
        if($mode === 'update_unit') {
            
            $unit_id = !empty($_REQUEST['unit_id']) ? $_REQUEST['unit_id'] :0;
            $data = !empty($_REQUEST['unit_data']) ? $_REQUEST['unit_data'] : [];
            $unit_id = fn_update_unit($data, $unit_id);
            if (!empty($unit_id)) {
            $suffix = ".update_unit?unit_id={$unit_id}";
            } else $suffix = ".add_unit";
    
        } elseif($mode ==='update_units') {
            if (!empty($_REQUEST['units_data'])){
                foreach ($_REQUEST['units_data'] as $unit_id => $data) {
                    fn_update_unit($data, $unit_id);
                }
            }
                $suffix = ".manage_units";
            
        } elseif($mode === 'delete_unit') {
            $unit_id = !empty($_REQUEST['unit_id']) ? $_REQUEST['unit_id'] :0;
            fn_delete_unit($unit_id);
            $suffix = ".manage_units";
        } elseif($mode == 'delete_units') {
           
            if (!empty($_REQUEST['units_ids'])) {
                foreach($_REQUEST['units_ids'] as $unit_id){
                    fn_delete_unit($unit_id); 
                }
            }
            $suffix = ".manage_units";
        }
    
    return [CONTROLLER_STATUS_OK, 'units' . $suffix];
}
    if($mode ==='update_unit' || $mode === 'add_unit') {
       
        $unit_id = !empty($_REQUEST['unit_id']) ? $_REQUEST['unit_id'] : 0;
        $unit_data = fn_get_unit_data($unit_id, DESCR_SL);
        

    if (empty($unit_data) && $mode === 'update') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }
    if ($mode='picker'){   

    Tygh::$app['view']->assign([
    
    'unit_data' => $unit_data,
    'boss_info' =>   !empty($unit_data['user_id']) ? fn_get_user_short_info($unit_data['user_id'], DESCR_SL) : [],
    'worker_info' => !empty($unit_data['user_id']) ? fn_get_user_short_info($unit_data['user_id'], DESCR_SL) : [],
    
    
]);    
    }
}
    
if($mode =='manage_units') {
   
    list($units, $search) = fn_get_units($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);
        
    Tygh::$app['view']->assign('units', $units);
    Tygh::$app['view']->assign('search', $search);
}

