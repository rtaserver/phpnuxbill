<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/
_admin();
$ui->assign('_title', $_L['Settings']);
$ui->assign('_system_menu', 'settings');

$action = $routes['1'];
$admin = Admin::_info();
$ui->assign('_admin', $admin);

switch ($action) {
    case 'app':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }

        if (!empty(_get('testWa'))) {
            $result = Message::sendWhatsapp(_get('testWa'), 'PHPNuxBill Test Whatsapp');
            r2(U . "settings/app", 's', 'Test Whatsapp has been send<br>Result: ' . $result);
        }
        if (!empty(_get('testSms'))) {
            $result = Message::sendSMS(_get('testSms'), 'PHPNuxBill Test SMS');
            r2(U . "settings/app", 's', 'Test SMS has been send<br>Result: ' . $result);
        }
        if (!empty(_get('testTg'))) {
            $result = Message::sendTelegram('PHPNuxBill Test Telegram');
            r2(U . "settings/app", 's', 'Test Telegram has been send<br>Result: ' . $result);
        }

        if (file_exists('system/uploads/logo.png')) {
            $logo = 'system/uploads/logo.png?' . time();
        } else {
            $logo = 'system/uploads/logo.default.png';
        }
        $ui->assign('logo', $logo);
        if ($_c['radius_enable'] && empty($_c['radius_client'])) {
            try {
                $_c['radius_client'] = Radius::getClient();
                $ui->assign('_c', $_c);
            } catch (Exception $e) {
                //ignore
            }
        }
        $themes = [];
        $files = scandir('ui/themes/');
        foreach ($files as $file) {
            if (is_dir('ui/themes/' . $file) && !in_array($file, ['.', '..'])) {
                $themes[] = $file;
            }
        }
        $r = ORM::for_table('tbl_routers')->find_many();
        $ui->assign('r', $r);
        if (function_exists("shell_exec")) {
            $php = trim(shell_exec('which php'));
            if (empty($php)) {
                $php = 'php';
            }
        } else {
            $php = 'php';
        }
        $ui->assign('php', $php);
        $ui->assign('dir', str_replace('controllers', '', __DIR__));
        $ui->assign('themes', $themes);
        run_hook('view_app_settings'); #HOOK
        $ui->display('app-settings.tpl');
        break;

    case 'localisation':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        $folders = [];
        $files = scandir('system/lan/');
        foreach ($files as $file) {
            if (is_dir('system/lan/' . $file) && !in_array($file, ['.', '..'])) {
                $folders[] = $file;
            }
        }
        $ui->assign('lan', $folders);
        $timezonelist = Timezone::timezoneList();
        $ui->assign('tlist', $timezonelist);
        $ui->assign('xjq', ' $("#tzone").select2(); ');
        run_hook('view_localisation'); #HOOK
        $ui->display('app-localisation.tpl');
        break;

    case 'users':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }

        $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/c/users.js"></script>');

        $username = _post('username');
        if ($username != '') {
            $paginator = Paginator::build(ORM::for_table('tbl_users'), ['username' => '%' . $username . '%'], $username);
            $d = ORM::for_table('tbl_users')->where_like('username', '%' . $username . '%')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_asc('id')->find_many();
        } else {
            $paginator = Paginator::build(ORM::for_table('tbl_users'));
            $d = ORM::for_table('tbl_users')->offset($paginator['startpoint'])->limit($paginator['limit'])->order_by_asc('id')->find_many();
        }

        $ui->assign('d', $d);
        $ui->assign('paginator', $paginator);
        run_hook('view_list_admin'); #HOOK
        $ui->display('users.tpl');
        break;

    case 'users-add':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        run_hook('view_add_admin'); #HOOK
        $ui->display('users-add.tpl');
        break;

    case 'users-edit':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }

        $id  = $routes['2'];
        $d = ORM::for_table('tbl_users')->find_one($id);
        if ($d) {
            $ui->assign('d', $d);
            run_hook('view_edit_admin'); #HOOK
            $ui->display('users-edit.tpl');
        } else {
            r2(U . 'settings/users', 'e', $_L['Account_Not_Found']);
        }
        break;

    case 'users-delete':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }

        $id  = $routes['2'];
        if (($admin['id']) == $id) {
            r2(U . 'settings/users', 'e', 'Sorry You can\'t delete yourself');
        }
        $d = ORM::for_table('tbl_users')->find_one($id);
        if ($d) {
            run_hook('delete_admin'); #HOOK
            $d->delete();
            r2(U . 'settings/users', 's', $_L['User_Delete_Ok']);
        } else {
            r2(U . 'settings/users', 'e', $_L['Account_Not_Found']);
        }
        break;

    case 'users-post':
        $username = _post('username');
        $fullname = _post('fullname');
        $password = _post('password');
        $cpassword = _post('cpassword');
        $user_type = _post('user_type');
        $msg = '';
        if (Validator::Length($username, 16, 2) == false) {
            $msg .= 'Username should be between 3 to 15 characters' . '<br>';
        }
        if (Validator::Length($fullname, 26, 2) == false) {
            $msg .= 'Full Name should be between 3 to 25 characters' . '<br>';
        }
        if (!Validator::Length($password, 15, 5)) {
            $msg .= 'Password should be between 6 to 15 characters' . '<br>';
        }
        if ($password != $cpassword) {
            $msg .= 'Passwords does not match' . '<br>';
        }

        $d = ORM::for_table('tbl_users')->where('username', $username)->find_one();
        if ($d) {
            $msg .= $_L['account_already_exist'] . '<br>';
        }
        $date_now = date("Y-m-d H:i:s");
        run_hook('add_admin'); #HOOK
        if ($msg == '') {
            $password = Password::_crypt($password);
            $d = ORM::for_table('tbl_users')->create();
            $d->username = $username;
            $d->fullname = $fullname;
            $d->password = $password;
            $d->user_type = $user_type;
            $d->status = 'Active';
            $d->creationdate = $date_now;

            $d->save();

            _log('[' . $admin['username'] . ']: ' . $_L['account_created_successfully'], 'Admin', $admin['id']);
            r2(U . 'settings/users', 's', $_L['account_created_successfully']);
        } else {
            r2(U . 'settings/users-add', 'e', $msg);
        }
        break;

    case 'users-edit-post':
        $username = _post('username');
        $fullname = _post('fullname');
        $password = _post('password');
        $cpassword = _post('cpassword');

        $msg = '';
        if (Validator::Length($username, 16, 2) == false) {
            $msg .= 'Username should be between 3 to 15 characters' . '<br>';
        }
        if (Validator::Length($fullname, 26, 2) == false) {
            $msg .= 'Full Name should be between 3 to 25 characters' . '<br>';
        }
        if ($password != '') {
            if (!Validator::Length($password, 15, 5)) {
                $msg .= 'Password should be between 6 to 15 characters' . '<br>';
            }
            if ($password != $cpassword) {
                $msg .= 'Passwords does not match' . '<br>';
            }
        }

        $id = _post('id');
        $d = ORM::for_table('tbl_users')->find_one($id);
        if ($d) {
        } else {
            $msg .= $_L['Data_Not_Found'] . '<br>';
        }

        if ($d['username'] != $username) {
            $c = ORM::for_table('tbl_users')->where('username', $username)->find_one();
            if ($c) {
                $msg .= $_L['account_already_exist'] . '<br>';
            }
        }
        run_hook('edit_admin'); #HOOK
        if ($msg == '') {
            $d->username = $username;
            if ($password != '') {
                $password = Password::_crypt($password);
                $d->password = $password;
            }

            $d->fullname = $fullname;
            if (($admin['id']) != $id) {
                $user_type = _post('user_type');
                $d->user_type = $user_type;
            }

            $d->save();

            _log('[' . $admin['username'] . ']: ' . $_L['User_Updated_Successfully'], 'Admin', $admin['id']);
            r2(U . 'settings/users', 's', 'User Updated Successfully');
        } else {
            r2(U . 'settings/users-edit/' . $id, 'e', $msg);
        }
        break;

    case 'app-post':
        $company = _post('CompanyName');
        run_hook('save_settings'); #HOOK


        if (!empty($_FILES['logo']['name'])) {
            if (function_exists('imagecreatetruecolor')) {
                if (file_exists('system/uploads/logo.png')) unlink('system/uploads/logo.png');
                File::resizeCropImage($_FILES['logo']['tmp_name'], 'system/uploads/logo.png', 1078, 200, 100);
                if (file_exists($_FILES['logo']['tmp_name'])) unlink($_FILES['logo']['tmp_name']);
            } else {
                r2(U . 'settings/app', 'e', 'PHP GD is not installed');
            }
        }
        if ($company == '') {
            r2(U . 'settings/app', 'e', $_L['All_field_is_required']);
        } else {
            if ($radius_enable) {
                try {
                    Radius::getTableNas()->find_many();
                } catch (Exception $e) {
                    $ui->assign("error_title", "RADIUS Error");
                    $ui->assign("error_message", "Radius table not found.<br><br>" .
                        $e->getMessage() .
                        "<br><br>Download <a href=\"https://raw.githubusercontent.com/hotspotbilling/phpnuxbill/Development/install/radius.sql\">here</a> or <a href=\"https://raw.githubusercontent.com/hotspotbilling/phpnuxbill/master/install/radius.sql\">here</a> and import it to database.<br><br>Check config.php for radius connection details");
                    $ui->display('router-error.tpl');
                    die();
                }
            }
            // save all settings
            foreach ($_POST as $key => $value) {
                $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
                if ($d) {
                    $d->value = $value;
                    $d->save();
                } else {
                    $d = ORM::for_table('tbl_appconfig')->create();
                    $d->setting = $key;
                    $d->value = $value;
                    $d->save();
                }
            }

            _log('[' . $admin['username'] . ']: ' . $_L['Settings_Saved_Successfully'], 'Admin', $admin['id']);

            r2(U . 'settings/app', 's', $_L['Settings_Saved_Successfully']);
        }
        break;

    case 'localisation-post':
        $tzone = _post('tzone');
        $date_format = _post('date_format');
        $country_code_phone = _post('country_code_phone');
        $lan = _post('lan');
        run_hook('save_localisation'); #HOOK
        if ($tzone == '' or $date_format == '' or $lan == '') {
            r2(U . 'settings/app', 'e', $_L['All_field_is_required']);
        } else {
            $d = ORM::for_table('tbl_appconfig')->where('setting', 'timezone')->find_one();
            $d->value = $tzone;
            $d->save();

            $d = ORM::for_table('tbl_appconfig')->where('setting', 'date_format')->find_one();
            $d->value = $date_format;
            $d->save();

            $dec_point = $_POST['dec_point'];
            if (strlen($dec_point) == '1') {
                $d = ORM::for_table('tbl_appconfig')->where('setting', 'dec_point')->find_one();
                $d->value = $dec_point;
                $d->save();
            }

            $thousands_sep = $_POST['thousands_sep'];
            if (strlen($thousands_sep) == '1') {
                $d = ORM::for_table('tbl_appconfig')->where('setting', 'thousands_sep')->find_one();
                $d->value = $thousands_sep;
                $d->save();
            }

            $d = ORM::for_table('tbl_appconfig')->where('setting', 'country_code_phone')->find_one();
            if ($d) {
                $d->value = $country_code_phone;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = 'country_code_phone';
                $d->value = $country_code_phone;
                $d->save();
            }

            $d = ORM::for_table('tbl_appconfig')->where('setting', 'radius_plan')->find_one();
            if ($d) {
                $d->value = _post('radius_plan');
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = 'radius_plan';
                $d->value = _post('radius_plan');
                $d->save();
            }
            $d = ORM::for_table('tbl_appconfig')->where('setting', 'hotspot_plan')->find_one();
            if ($d) {
                $d->value = _post('hotspot_plan');
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = 'hotspot_plan';
                $d->value = _post('hotspot_plan');
                $d->save();
            }
            $d = ORM::for_table('tbl_appconfig')->where('setting', 'pppoe_plan')->find_one();
            if ($d) {
                $d->value = _post('pppoe_plan');
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = 'pppoe_plan';
                $d->value = _post('pppoe_plan');
                $d->save();
            }

            $currency_code = $_POST['currency_code'];
            $d = ORM::for_table('tbl_appconfig')->where('setting', 'currency_code')->find_one();
            $d->value = $currency_code;
            $d->save();

            $d = ORM::for_table('tbl_appconfig')->where('setting', 'language')->find_one();
            $d->value = $lan;
            $d->save();

            _log('[' . $admin['username'] . ']: ' . $_L['Settings_Saved_Successfully'], 'Admin', $admin['id']);
            r2(U . 'settings/localisation', 's', $_L['Settings_Saved_Successfully']);
        }
        break;

    case 'change-password':
        if ($admin['user_type'] != 'Admin' and $admin['user_type'] != 'Sales') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        run_hook('view_change_password'); #HOOK
        $ui->display('change-password.tpl');
        break;

    case 'change-password-post':
        $password = _post('password');
        if ($password != '') {
            $d = ORM::for_table('tbl_users')->where('username', $admin['username'])->find_one();
            run_hook('change_password'); #HOOK
            if ($d) {
                $d_pass = $d['password'];
                if (Password::_verify($password, $d_pass) == true) {
                    $npass = _post('npass');
                    $cnpass = _post('cnpass');
                    if (!Validator::Length($npass, 15, 5)) {
                        r2(U . 'settings/change-password', 'e', 'New Password must be 6 to 14 character');
                    }
                    if ($npass != $cnpass) {
                        r2(U . 'settings/change-password', 'e', 'Both Password should be same');
                    }

                    $npass = Password::_crypt($npass);
                    $d->password = $npass;
                    $d->save();

                    _msglog('s', $_L['Password_Changed_Successfully']);
                    _log('[' . $admin['username'] . ']: Password changed successfully', 'Admin', $admin['id']);

                    r2(U . 'admin');
                } else {
                    r2(U . 'settings/change-password', 'e', $_L['Incorrect_Current_Password']);
                }
            } else {
                r2(U . 'settings/change-password', 'e', $_L['Incorrect_Current_Password']);
            }
        } else {
            r2(U . 'settings/change-password', 'e', $_L['Incorrect_Current_Password']);
        }
        break;

    case 'notifications':
        if ($admin['user_type'] != 'Admin' and $admin['user_type'] != 'Sales') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        run_hook('view_notifications'); #HOOK
        if (file_exists("system/uploads/notifications.json")) {
            $ui->assign('_json', json_decode(file_get_contents('system/uploads/notifications.json'), true));
        } else {
            $ui->assign('_json', json_decode(file_get_contents('system/uploads/notifications.default.json'), true));
        }
        $ui->assign('_default', json_decode(file_get_contents('system/uploads/notifications.default.json'), true));
        $ui->display('app-notifications.tpl');
        break;
    case 'notifications-post':
        file_put_contents("system/uploads/notifications.json", json_encode($_POST));
        r2(U . 'settings/notifications', 's', $_L['Settings_Saved_Successfully']);
        break;
    case 'dbstatus':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }

        $dbc = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($result = $dbc->query('SHOW TABLE STATUS')) {
            $tables = array();
            while ($row = $result->fetch_array()) {
                $tables[$row['Name']]['rows'] = ORM::for_table($row["Name"])->count();
                $tables[$row['Name']]['name'] = $row["Name"];
            }
            $ui->assign('tables', $tables);
            run_hook('view_database'); #HOOK
            $ui->display('dbstatus.tpl');
        }
        break;

    case 'dbbackup':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        $tables = $_POST['tables'];
        set_time_limit(-1);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Disposition: attachment;filename="phpnuxbill_' . count($tables) . '_tables_' . date('Y-m-d_H_i') . '.json"');
        header('Content-Transfer-Encoding: binary');
        $array = [];
        foreach ($tables as $table) {
            $array[$table] = ORM::for_table($table)->find_array();
        }
        echo json_encode($array);
        break;
    case 'dbrestore':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        if (file_exists($_FILES['json']['tmp_name'])) {
            $suc = 0;
            $fal = 0;
            $json = json_decode(file_get_contents($_FILES['json']['tmp_name']), true);
            foreach ($json as $table => $records) {
                ORM::raw_execute("TRUNCATE $table;");
                foreach ($records as $rec) {
                    $t = ORM::for_table($table)->create();
                    foreach ($rec as $k => $v) {
                        if ($k != 'id') {
                            $t->set($k, $v);
                        }
                    }
                    if ($t->save()) {
                        $suc++;
                    } else {
                        $fal++;
                    }
                }
            }
            if (file_exists($_FILES['json']['tmp_name'])) unlink($_FILES['json']['tmp_name']);
            r2(U . "settings/dbstatus", 's', "Restored $suc success $fal failed");
        } else {
            r2(U . "settings/dbstatus", 'e', 'Upload failed');
        }
        break;
    case 'language':
        if ($admin['user_type'] != 'Admin') {
            r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
        }
        run_hook('view_add_language'); #HOOK
        $ui->display('language-add.tpl');
        break;

    case 'lang-post':
        $name = _post('name');
        $folder = _post('folder');
        $translator = _post('translator');

        if ($name == '' or $folder == '') {
            $msg .= $_L['All_field_is_required'] . '<br>';
        }

        $d = ORM::for_table('tbl_language')->where('name', $name)->find_one();
        if ($d) {
            $msg .= $_L['Lang_already_exist'] . '<br>';
        }
        run_hook('save_language'); #HOOK
        if ($msg == '') {
            $b = ORM::for_table('tbl_language')->create();
            $b->name = $name;
            $b->folder = $folder;
            $b->author = $translator;
            $b->save();

            r2(U . 'settings/localisation', 's', $_L['Created_Successfully']);
        } else {
            r2(U . 'settings/language', 'e', $msg);
        }
        break;

    default:
        $ui->display('a404.tpl');
}
