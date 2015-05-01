<?php if ( ! defined('OC_ADMIN')) exit('Direct access is not allowed.');

$trezor_linked = false;
$trezor = ModelTrezor::newInstance()->findByUser(osc_logged_admin_id(), 1);

if(Params::getParam('unlink')!='') {
    if(isset($trezor['s_address']) && substr($trezor['s_address'], 0, 10)==Params::getParam('unlink')) {
        ModelTrezor::newInstance()->delete(array('fk_i_user_id' => osc_logged_admin_id(), 'b_admin' => 1));
        ob_get_clean();
        osc_redirect_to(osc_route_url('trezor-admin-manage'));
    }
}

if(isset($trezor['s_address'])) {
    $trezor_linked = true;
}

?>

    <div class="form-row">
        <div class="form-label"><?php _e('TREZOR device', 'trezor'); ?></div>
        <?php if($trezor_linked) {
            echo '<div class="form-controls">' . sprintf(__('TREZOR device is linked to your account. <a href="%s" onclick="return confirm(\'Are you sure to unlink your account?\');" >Unlink account</a>', 'trezor'), osc_admin_base_url(true) . '?page=admins&action=edit&unlink=' . substr(@$trezor['s_address'], 0, 10)) . '</div></div>';
        } else {
            echo '<div class="form-controls">' . __('To link your TREZOR device with your current account you need to click on the button and connect your device.', 'trezor') . '</div></div>';
            echo '<div class="form-row"><div class="form-label"></div><div class="form-controls">';
                trezor_button('trezor_link', true);
            echo '</div></div>';
        };