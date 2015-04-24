<?php
/*
Plugin Name: Trezor Connect
Plugin URI: trezor
Description: Use Trezor Connect as a login for your users
Version: 1.0.0
Author: _CONEJO
Author URI: http://www.conejo.me/
Short Name: trezor
Plugin update URI: trezor
*/

    define('TREZOR_PATH', PLUGINS_PATH . 'trezor/');
    require_once TREZOR_PATH . "BitcoinECDSA.php";
    require_once TREZOR_PATH . '/ModelTrezor.php';

    function trezor_install() {
        require_once dirname(__FILE__) . '/ModelTrezor.php';
        ModelTrezor::newInstance()->install();
    }

    function trezor_uninstall() {
        require_once dirname(__FILE__) . '/ModelTrezor.php';
        ModelTrezor::newInstance()->uninstall();
    }

    osc_add_hook('trezor_button', 'trezor_button');
    function trezor_button($hook = 'trezor') {
        if($hook!='trezor_link') {
            $hook = 'trezor';
        }
        if(($hook=='trezor' && !osc_is_web_user_logged_in()) || $hook=='trezor_link') {
            ?>
            <div id="trezor_button">
                <script type="text/javascript">
                    function trezorLogin(response) {
                        if (response.success) {
                            console.log(response);
                            $.post(
                                '<?php echo osc_base_url(true); ?>',
                                {
                                    page: 'ajax',
                                    action: 'runhook',
                                    hook: '<?php echo $hook; ?>',
                                    challenge_hidden: response.challenge_hidden,
                                    challenge_visual: response.challenge_visual,
                                    public_key: response.public_key,
                                    signature: response.signature
                                },
                                function (response) {
                                    if (response.success) {
                                        window.location.reload(false);
                                    } else {
                                        alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.error);
                                    }
                                },
                                'json'
                            );
                        } else {
                            alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.error);
                        }
                    }
                </script>
                <trezor:login callback="trezorLogin" icon="<?php echo trezor_logo(); ?>">
                </trezor:login>


                <script type="text/javascript">
                    var elements = document.getElementsByTagName('trezor:login');
                    var origin = 'https://trezor.github.io';
                    var connect_path = origin + '/connect/';
                    var content = '<a style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; display: block; padding: 6px 12px; margin-bottom: 0; font-weight: normal; line-height: 1.42857143; text-align: center; white-space: nowrap; vertical-align: middle; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; border: 1px solid transparent; border-radius: 4px; text-decoration: none; position:relative; padding-left:44px; width:136px; color:#fff; background-color:#59983b; border-color:rgba(0,0,0,0.2);" onmouseover="this.style.background=\'#43732d\';" onmouseout="this.style.background=\'#59983b\';" onclick="trezor_login_handler();"><span style="position:absolute; left:0; top:0; bottom:0; width:32px; line-height:34px; font-size:1.6em; text-align:center; border-right:1px solid rgba(0,0,0,0.2); background: url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABWklEQVRYw+2WPUsDQRCGZ0w0RRQUCwUllT9AFAQra/+BjYiQRgRBBFv/goWFlVjaBm1SCVpZiIKgha1iI4iFXxDz2MzBEe6SveOWIOwLx3I7X+/NzuyNSEBADgDrQAN4Al6AU2ADKPkOPAk0ScclUPNJIB78ETgEDoD72P6Vl0xY2iMcA5WYrGxEImz7INCIfXklQV4CbkznwtXvQAYO87Y2VfWnU6iqvyJyZq+zrseQhcCUrc9ddCLZiD2FEuhbzy92FJgrjoClXv7VgcCriIzn5P+hqsPdFMoOTgZT9r9E5NOOsSoiQzn998zAeyylC0AV0JQ2HAOWgVuz+S6SwEoGmxNXAn3vgkAgEPgXV3HUhg/ALjDdRXcO2AfefNwDEdrAOVAHRm1M2wHuEv4HhRBYtVGslRQgZb9lNmtFD6RbNvMloW1D6SYw4bsuZoA9q4trq42aBATkwB8MsRcDkn7CNQAAAABJRU5ErkJggg==\') no-repeat;"></span><?php printf(__('Sign in with %s', 'trezor'), '<strong>TREZOR</strong>'); ?></a><span style="display: block;font-family: Helvetica, Arial, sans-serif; font-size: 9px; width: 192px; text-align: right; margin-top: 2px;"><a href="https://www.bitcointrezor.com/" target="_blank" style="text-decoration: none; color: #59983b;"><?php _e('What is TREZOR?', 'trezor'); ?></a></span>';

                    for (var i = 0; i < elements.length; i++) {
                        var e = elements[i];
                        window.connect_data = {
                            'callback': e.getAttribute('callback'),
                            'hosticon': e.getAttribute('icon'),
                            'challenge_hidden': e.getAttribute('challenge_hidden') || Array.apply(null, Array(64)).map(function () {
                                return Math.floor(Math.random() * 16).toString(16);
                            }).join(''),
                            'challenge_visual': e.getAttribute('challenge_visual') || new Date().toISOString().substring(0, 19).replace('T', ' ')
                        };
                        e.parentNode.innerHTML = content;
                    }

                    function receiveMessage(event) {
                        if (event.origin !== origin) return;
                        window[window.connect_data.callback](event.data);
                    }

                    window.addEventListener('message', receiveMessage, false);

                    function trezor_login_handler() {
                        var w = 500, h = 400, x = (screen.width - w) / 2, y = (screen.height - h) / 3;
                        var popup = window.open(connect_path + 'login.html', 'trezor_login_window', 'height=' + h + ',width=' + w + ',left=' + x + ',top=' + y + ',menubar=no,toolbar=no,location=no,personalbar=no,status=no');
                        // give some time to popup to open, then send request
                        setTimeout(function () {
                            var request = {};
                            request.trezor_login = true;
                            request.challenge_hidden = window.connect_data.challenge_hidden;
                            request.challenge_visual = window.connect_data.challenge_visual;
                            request.icon = window.connect_data.hosticon;
                            popup.postMessage(request, origin);
                        }, 1500);
                    }
                </script>
            </div>
        <?php
        };
    }

    function trezor_ajax() {

        $challenge_hidden = Params::getParam('challenge_hidden');
        $challenge_visual = Params::getParam('challenge_visual');
        $public_key = strtolower(Params::getParam('public_key'));
        $signature = strtolower(Params::getParam('signature'));

        $message = hex2bin($challenge_hidden) . $challenge_visual;

        $R = substr($signature, 2, 64);
        $S = substr($signature, 66, 64);

        $ecdsa = new BitcoinECDSA();
        $hash = $ecdsa->hash256("\x18Bitcoin Signed Message:\n" . $ecdsa->numToVarIntString(strlen($message)) . $message);

        $success = (bool)$ecdsa->checkSignaturePoints($public_key, $R, $S, $hash);

        if($success) {
            $address = $ecdsa->getAddress($public_key);
            $user = ModelTrezor::newInstance()->findByPrimaryKey($address);
            if(isset($user['fk_i_user_id'])) {
                require_once LIB_PATH . 'osclass/UserActions.php';
                $uActions = new UserActions(false);
                $logged = $uActions->bootstrap_login($user['fk_i_user_id']);

                if($logged==0) {
                    echo json_encode(array('success' => false, 'error' => __("The user doesn't exist", 'trezor')));
                } else if($logged==1) {
                    echo json_encode(array('success' => false, 'error' => __('The user has not been validated yet', 'trezor')));
                } else if($logged==2) {
                    echo json_encode(array('success' => false, 'error' => __('The user has been suspended', 'trezor')));
                } else if($logged==3) {
                    echo json_encode(array('success' => true, 'error' => __('The user has been signed in correctly', 'trezor')));
                } else {
                    echo json_encode(array('success' => false, 'error' => __('This should never happen', 'trezor')));
                }
                die;
            }
            echo json_encode(array('success' => false, 'error' => __('Address is not linked with any user. Sign in with your password and link it with your Trezor device', 'trezor')));
        } else {
            echo json_encode(array('success' => false, 'error' => __('Wrong signature', 'trezor')));
        }
        die;

    }
    osc_add_hook('ajax_trezor', 'trezor_ajax');

    function trezor_ajax_link() {

        $challenge_hidden = Params::getParam('challenge_hidden');
        $challenge_visual = Params::getParam('challenge_visual');
        $public_key = strtolower(Params::getParam('public_key'));
        $signature = strtolower(Params::getParam('signature'));

        $message = hex2bin($challenge_hidden) . $challenge_visual;

        $R = substr($signature, 2, 64);
        $S = substr($signature, 66, 64);

        $ecdsa = new BitcoinECDSA();
        $hash = $ecdsa->hash256("\x18Bitcoin Signed Message:\n" . $ecdsa->numToVarIntString(strlen($message)) . $message);

        $success = (bool)$ecdsa->checkSignaturePoints($public_key, $R, $S, $hash);

        if($success) {
            $address = $ecdsa->getAddress($public_key);
            $user = ModelTrezor::newInstance()->findByPrimaryKey($address);
            if(!isset($user['fk_i_user_id']) && osc_is_web_user_logged_in()) {
                ModelTrezor::newInstance()->insert(
                    array(
                        'fk_i_user_id' => osc_logged_user_id(),
                        's_address' => $address
                    )
                );

                echo json_encode(array('success' => true, 'error' => __('Account linked correctly', 'trezor')));
                die;
            }
            echo json_encode(array('success' => false, 'error' => __('Address already linked to an user', 'trezor')));
        } else {
            echo json_encode(array('success' => false, 'error' => __('Wrong signature', 'trezor')));
        }
        die;

    }
    osc_add_hook('ajax_trezor_link', 'trezor_ajax_link');

    function trezor_user_menu($options) {
        $options[] = array('name' => __('Trezor device'), 'url' => osc_route_url('trezor-manage'), 'class' => 'opt_trezor');
        return $options;
    }
    osc_add_hook('user_menu_filter', 'trezor_user_menu');

    function trezor_admin_menu() {
        osc_add_admin_submenu_divider('plugins', 'Trezor plugin', 'trezor_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Help - How to use', 'trezor'), osc_route_admin_url('trezor-admin-help'), 'trezor_help', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Configure Trezor login', 'trezor'), osc_route_admin_url('trezor-admin-conf'), 'trezor_conf', 'administrator');
    }
    osc_add_hook('admin_menu_init', 'trezor_admin_menu');


    function trezor_actions_admin() {
        switch( Params::getParam('action_specific') ) {
            case('trezor_upload_logo'):
                $package = Params::getFiles('logo');
                if( $package['error'] == UPLOAD_ERR_OK ) {

                    $files = glob(osc_content_path().'uploads/trezor_logo*');
                    if(is_array($files)) {
                        foreach($files as $file) {
                            @unlink($file);
                        }
                    }

                    $img = ImageResizer::fromFile($package['tmp_name']);
                    $ext = $img->getExt();
                    $logo_name = 'trezor_logo';
                    $logo_name .= '.'.$ext;
                    $path = osc_uploads_path() . $logo_name ;
                    $img->resizeTo(128, 128, false, true)->saveToFile($path, $ext);

                    osc_set_preference('logo', osc_base_url() . 'oc-content/uploads/' . $logo_name, 'trezor');

                    osc_add_flash_ok_message(__('The logo image has been uploaded correctly', 'trezor'), 'admin');
                } else {
                    osc_add_flash_error_message(__("An error has occurred, please try again", 'trezor'), 'admin');
                }
                osc_redirect_to(osc_route_admin_url('trezor-conf'));
                break;
            case('trezor_remove'):

                $files = glob(osc_content_path().'uploads/trezor_logo*');
                if(is_array($files)) {
                    foreach($files as $file) {
                        @unlink($file);
                    }
                }

                osc_set_preference('logo', '', 'trezor');
                osc_reset_preferences();
                osc_add_flash_ok_message(__('The logo image has been removed', 'trezor'), 'admin');
                osc_redirect_to(osc_route_admin_url('trezor-conf'));
                break;
            default:
                break;
        }
    }
    osc_add_hook('init_admin', 'trezor_actions_admin');

    function trezor_logo() {
        if(osc_get_preference('logo', 'trezor')!='') {
            return osc_get_preference('logo', 'trezor');
        }
        return osc_plugin_url(__FILE__) . 'img/logo.png';
    }


    osc_add_route('trezor-admin-help', 'trezor/admin/help', 'trezor/admin/help', osc_plugin_folder(__FILE__).'views/admin/help.php');
    osc_add_route('trezor-admin-conf', 'trezor/admin/conf', 'trezor/admin/conf', osc_plugin_folder(__FILE__).'views/admin/conf.php');
    osc_add_route('trezor-manage', 'trezor/manage', 'trezor/manage', osc_plugin_folder(__FILE__).'views/user/manage.php', true);

    osc_register_plugin(osc_plugin_path(__FILE__), 'trezor_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'trezor_uninstall');

