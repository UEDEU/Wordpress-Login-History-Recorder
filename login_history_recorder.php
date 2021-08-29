<?php
/**
* Plugin Name: Login History Recorder
* Description: ログイン履歴の一覧
* Version: 0.1
* Author: Edge
* Author Uri:
*/



add_filter('manage_users_columns', 'create_columns');
add_filter('manage_users_custom_column', 'show_login_time', 10, 3);
add_filter('manage_users_custom_column', 'show_ip', 9, 3);
add_action( 'wp_login', 'record_login_info', 10, 2 );
add_action( 'wp_login', 'record_ip', 10, 2 );

function record_login_info($login, $user){
        update_user_meta($user->ID, 'last_login', time());
        update_user_meta($user->ID, 'ip', get_ip());
}

function get_ip(){
        return $_SERVER['REMOTE_ADDR'];
}

function create_columns($columns){
        foreach($columns as $key => $column) {
                if ($key == 'role') {
                        $columns['last_login'] = '最終ログイン日時(UTC)';
                        $columns['ip'] = '接続元IP';
                }
        }
        return $columns;
}

function show_login_time($value, $column_name, $user_id){

        if ($column_name != 'last_login') {
                return $value;
        }

        $last_login = get_user_meta($user_id, 'last_login', true);

        if (!$last_login) {
                return 'まだ記録されていません';
        }

        return date(get_option('date_format') . ' ' . get_option('time_format'), (int)$last_login);
}

function show_ip($value, $column_name, $user_id){

        if ($column_name != 'ip'){
                return false;
        }

        $last_login = get_user_meta($user_id, 'ip', true);

        if(!$last_login){
                return 'まだ記録されていません';
        }

        return get_ip();
}
