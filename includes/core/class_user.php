<?php

class User
{
    // GENERAL

    public static function user_info($d)
    {
        // vars
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $phone = isset($d['phone']) ? preg_replace('~\D+~', '', $d['phone']) : 0;
        // where
        if ($user_id) {
            $where = "user_id='" . $user_id . "'";
        } elseif ($phone) {
            $where = "phone='" . $phone . "'";
        } else {
            return [];
        }
        // info
        $q = DB::query('SELECT user_id, phone, access FROM users WHERE ' . $where . ' LIMIT 1;') or die(DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'access' => (int) $row['access']
            ];
        } else {
            return [
                'id' => 0,
                'access' => 0
            ];
        }
    }

    public static function users_list_plots($number)
    {
        // vars
        $items = [];
        // info
        $q = DB::query("SELECT user_id, plot_id, first_name, email, phone
            FROM users WHERE plot_id LIKE '%" . $number . "%' ORDER BY user_id;") or die(DB::error());
        while ($row = DB::fetch_row($q)) {
            $plot_ids = explode(',', $row['plot_id']);
            $val = false;
            foreach ($plot_ids as $plot_id) {
                if ($plot_id == $number) {
                    $val = true;
                }
            }
            if ($val) {
                $items[] = [
                    'id' => (int) $row['user_id'],
                    'first_name' => $row['first_name'],
                    'email' => $row['email'],
                    'phone_str' => phone_formatting($row['phone'])
                ];
            }
        }
        // output
        return $items;
    }

    public static function users_list($d = [])
    {
        // vars
        $search = isset($d['search']) && trim($d['search']) ? $d['search'] : '';
        $offset = isset($d['offset']) && is_numeric($d['offset']) ? $d['offset'] : 0;
        $limit = 20;
        $items = [];
        // where
        $where = [];
        if ($search) {
            $where[] = "LIKE '%" . $search . "%'";
        }
        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        // info
        $q = DB::query('SELECT user_id, plot_id, first_name, last_name, phone, email, last_login
            FROM users ' . $where . ' ORDER BY user_id LIMIT ' . $offset . ', ' . $limit . ';') or die(DB::error());
        while ($row = DB::fetch_row($q)) {
            $items[] = [
                'user_id' => (int) $row['user_id'],
                'plot_id' => (int) $row['plot_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'last_login' => date('Y/m/d H:m:s', $row['last_login'])
            ];
        }
        // paginator
        $q = DB::query('SELECT count(*) FROM users ' . $where . ';');
        $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
        $url = 'users?';
        if ($search) {
            $url .= '&search=' . $search;
        }
        paginator($count, $offset, $limit, $url, $paginator);
        // output
        return ['items' => $items, 'paginator' => $paginator];
    }

    public static function user_info_full($user_id) {
        $q = DB::query("SELECT user_id, first_name, last_name, phone, email
            FROM users WHERE user_id='".$user_id."' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
            ];
        } else {
            return [
                'id' => 0,
                'first_name' => 0,
                'last_name' => 0,
                'phone' => '',
                'email' => '',
            ];
        }
    }

    public static function user_edit_window($d = [])
    {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        HTML::assign('user', User::user_info_full($user_id));
        return ['html' => HTML::fetch('./partials/user_edit.html')];
    }

    public static function user_edit_update($d = [])
    {
    }
}
