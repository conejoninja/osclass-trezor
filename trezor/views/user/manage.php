<?php

$trezor_linked = false;
$trezor = ModelTrezor::newInstance()->findByUser(osc_logged_user_id());

if(Params::getParam('unlink')!='') {
    if(isset($trezor['s_address']) && substr($trezor['s_address'], 0, 10)==Params::getParam('unlink')) {
        ModelTrezor::newInstance()->delete(array('fk_i_user_id' => osc_logged_user_id(), 'b_admin' => 0));
        ob_get_clean();
        osc_redirect_to(osc_route_url('trezor-manage'));
    }
}

if(isset($trezor['s_address'])) {
    $trezor_linked = true;
}

?><div class="wrapper wrapper-flash">
    <div id="flash_js"></div>
</div>
<h2><?php _e('TREZOR device', 'trezor'); ?></h2>
<?php if($trezor_linked) {

    echo '<div>' . sprintf(__('TREZOR device is linked to your account. <a href="%s" onclick="return confirm(\'Are you sure to unlink your account?\');" >Unlink account</a>', 'trezor'), osc_route_url('trezor-manage') . '?unlink=' . substr(@$trezor['s_address'], 0, 10)) . '</div>';

} else {

    echo '<div>' . __('To link your TREZOR device with your current account you need to click on the button and connect your device.', 'trezor') . '</div>';
    echo '<br/>';
    echo '<div>';
        trezor_button('trezor_link');
    echo '</div>';


};
