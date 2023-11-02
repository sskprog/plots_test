<?php

function controller_user($act, $d)
{
    if ($act == 'edit_window') {
        return User::plot_edit_window($d);
    }
    if ($act == 'edit_update') {
        return User::plot_edit_update($d);
    }
    return '';
}
