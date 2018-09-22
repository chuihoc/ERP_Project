<?php
// PSR 7 standard.
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Helper\Roles;

//User router
//$app->get('/login', 'UserController:index');


$app->post('/token', 'UserController:token')->setName('token');
$app->get('/fetchRoles', 'UserController:fetchRoles');//Per User
$app->get('/fetchAllRoles/{user_id}', 'UserController:fetchAllRoles');// To assign to user
$app->get('/fetchLang', 'LanguageController:fetchLang');//get language info

//Upload router
$app->post('/uploadFile', 'UploadController:uploadFile');
//User routers
$app->get('/users/fetchUsers', 'UserController:fetchUsers')->setName('users__users__view');
$app->post('/users/updateUser', 'UserController:updateUser')->setName('users__users__update');
$app->post('/users/updateUserByUser', 'UserController:updateUserByUser');
$app->get('/users/deleteUser/{id}', 'UserController:deleteUser')->setName('users__users__delete');
$app->post('/users/updatePermission', 'UserController:updatePermission');
$app->get('/dashboard', 'HomeController:index');
//Npp router
$app->get('/nha_cung_cap/fetchNpp', 'NhaCungCapController:fetchNpp');
$app->post('/nha_cung_cap/updateNpp', 'NhaCungCapController:updateNpp');
$app->get('/nha_cung_cap/deleteNpp/{id}', 'NhaCungCapController:deleteNpp');
$app->get('/', 'HomeController:index');
//Khach hàng router
$app->get('/qlkh/fetchKh', 'KhController:fetchKh')->setName(Roles::roleAndRouter()['qlkh']['view']);
$app->post('/qlkh/updateKh', 'KhController:updateKh')->setName(Roles::roleAndRouter()['qlkh']['add']);
$app->get('/qlkh/deleteKh/{id}', 'KhController:deleteKh')->setName(Roles::roleAndRouter()['qlkh']['delete']);
//Đơn hàng router
$app->get('/order/fetchDh', 'OrderController:fetchDh')->setName(Roles::roleAndRouter()['qldh']['view']);
$app->post('/order/updateDh', 'OrderController:updateDh')->setName(Roles::roleAndRouter()['qldh']['add']);
$app->get('/order/deleteDh/{id}', 'OrderController:deleteDh')->setName(Roles::roleAndRouter()['qldh']['delete']);
//Product router
$app->get('/product/fetch', 'ProductController:fetch')->setName(Roles::roleAndRouter()['product']['view']);
$app->post('/product/update', 'ProductController:update')->setName(Roles::roleAndRouter()['product']['add']);
$app->get('/product/delete/{id}', 'ProductController:delete')->setName(Roles::roleAndRouter()['product']['delete']);
//Nhân sự router
$app->get('/qlns/fetchNs', 'NhansuController:fetchNs')->setName(Roles::roleAndRouter()['qlns']['view']);
$app->post('/qlns/updateNs', 'NhansuController:updateNs')->setName(Roles::roleAndRouter()['qlns']['add']);
$app->get('/qlns/deleteNs/{id}', 'NhansuController:deleteNs')->setName(Roles::roleAndRouter()['qlns']['delete']);
//Công việc router
$app->get('/qljobs/fetchJob', 'JobsController:fetchJob')->setName(Roles::roleAndRouter()['qljobs']['view']);
$app->post('/qljobs/updateJob', 'JobsController:updateJob')->setName(Roles::roleAndRouter()['qljobs']['add']);
$app->get('/qljobs/deleteJob/{id}', 'JobsController:deleteJob')->setName(Roles::roleAndRouter()['qljobs']['delete']);
//Quy trinh san xuat router
$app->get('/quytrinhsx/fetch', 'QuytrinhSxController:fetch')->setName(Roles::roleAndRouter()['quy_trinh_sx']['view']);
$app->post('/quytrinhsx/update', 'QuytrinhSxController:update')->setName(Roles::roleAndRouter()['quy_trinh_sx']['add']);
$app->get('/quytrinhsx/delete/{id}', 'QuytrinhSxController:delete')->setName(Roles::roleAndRouter()['quy_trinh_sx']['delete']);
//Gantt router
$app->post('/gantt/update', 'GanttController:update');
$app->post('/gantt/updateLink', 'GanttController:updateLink');
$app->get('/gantt/deleteLink/{id}', 'GanttController:deleteLink');
$app->get('/gantt/fetchTasks/{quy_trinh_id}', 'GanttController:fetchTasks');
$app->get('/gantt/fetchTasksByMaSx/{ma_sx}', 'GanttController:fetchTasksByMaSx');
$app->get('/gantt/fetchTasksByMaOrder/{ma_order}', 'GanttController:fetchTasksByMaOrder');
$app->get('/gantt/fetchTasksFromSample/{ma_sx}/{quy_trinh_id}/{nsx}', 'GanttController:fetchTasksFromSample');
$app->get('/gantt/fetchMyTasks', 'GanttController:fetchMyTasks');
$app->get('/gantt/delete/{id}', 'GanttController:delete');
$app->get('/gantt/allPlan', 'GanttController:getAllPlanData');
$app->get('/gantt/users', 'GanttController:getUsers');

//Phòng ban router
$app->get('/qlpb/fetchPb', 'PhongbanController:fetchPb')->setName(Roles::roleAndRouter()['qlpb']['view']);
$app->post('/qlpb/updatePb', 'PhongbanController:updatePb')->setName(Roles::roleAndRouter()['qlpb']['add']);
$app->get('/qlpb/deletePb/{id}', 'PhongbanController:deletePb')->setName(Roles::roleAndRouter()['qlpb']['delete']);
$app->get('/qlpb/fetchGroupRoles', 'PhongbanController:fetchGroupRoles')->setName(Roles::roleAndRouter()['qlpb']['view']);
//Phân Quyền router
$app->get('/qlpq/fetch', 'PhanquyenController:fetch')->setName(Roles::roleAndRouter()['qluser']['view']);
$app->post('/qlpq/update', 'PhanquyenController:update')->setName(Roles::roleAndRouter()['qluser']['add']);
$app->get('/qlpq/delete/{id}', 'PhanquyenController:delete')->setName(Roles::roleAndRouter()['qluser']['delete']);
//Language router
$app->get('/lang/fetchLang', 'LanguageController:fetchLang')->setName(Roles::roleAndRouter()['lang']['view']);
$app->get('/lang/fetchListLang', 'LanguageController:fetchListLang')->setName(Roles::roleAndRouter()['lang']['view']);
$app->post('/lang/updateLang', 'LanguageController:updateLang')->setName(Roles::roleAndRouter()['lang']['add']);
$app->get('/lang/deleteLang/{id}', 'LanguageController:deleteLang')->setName(Roles::roleAndRouter()['lang']['delete']);
//Note router
$app->get('/note/fetchNote', 'NoteController:fetchNote')->setName(Roles::roleAndRouter()['note']['view']);
$app->post('/note/updateNote', 'NoteController:updateNote')->setName(Roles::roleAndRouter()['note']['add']);
$app->get('/note/deleteNote/{id}', 'NoteController:deleteNote')->setName(Roles::roleAndRouter()['note']['delete']);
$app->post('/exportExcel', 'ExportController:export');
//Options router
$app->get('/opts/fetchOpts', 'OptionsController:fetchOpts')->setName(Roles::roleAndRouter()['options']['view']);
$app->post('/opts/updateOpts', 'OptionsController:updateOpts')->setName(Roles::roleAndRouter()['options']['add']);
$app->get('/opts/deleteOpts/{id}', 'OptionsController:deleteOpts')->setName(Roles::roleAndRouter()['options']['delete']);
//Request Order router
$app->get('/request_order/fetch', 'RequestOrderController:fetch')->setName(Roles::roleAndRouter()['request_order']['view']);
$app->get('/request_order/fetchProductDetailsList', 'RequestOrderController:fetchProductDetailsList')->setName(Roles::roleAndRouter()['request_order']['view']);
$app->get('/request_order/fetchSelectedProduct/{ma_order}', 'RequestOrderController:fetchSelectedProduct')->setName(Roles::roleAndRouter()['request_order']['view']);
$app->get('/request_order/fetchProductByCate/{cate_id}', 'RequestOrderController:fetchProductByCate')->setName(Roles::roleAndRouter()['request_order']['view']);
$app->post('/request_order/update', 'RequestOrderController:update')->setName(Roles::roleAndRouter()['request_order']['add']);
$app->post('/request_order/updateProduct', 'RequestOrderController:updateProduct')->setName(Roles::roleAndRouter()['request_order']['add']);
$app->post('/request_order/pheduyet', 'RequestOrderController:pheDuyet');
$app->get('/request_order/delete/{id}', 'RequestOrderController:delete')->setName(Roles::roleAndRouter()['request_order']['delete']);
$app->get('/request_order/deleteProduct/{id}', 'RequestOrderController:deleteProduct')->setName(Roles::roleAndRouter()['request_order']['delete']);
