<?php
/*
Plugin Name: Trezor Connect
Plugin URI: trezor
Description: Use Trezor Connect as a login for your users
Version: 1.3.0
Author: _CONEJO
Author URI: http://www.conejo.me/
Short Name: trezor
Plugin update URI: trezor
*/

    define('TREZOR_PATH', PLUGINS_PATH . 'trezor/');
    require_once TREZOR_PATH . "BitcoinECDSA.php";
    require_once TREZOR_PATH . '/ModelTrezor.php';

    function trezor_install() {
        require_once TREZOR_PATH . '/ModelTrezor.php';
        ModelTrezor::newInstance()->install();
    }

    function trezor_uninstall() {
        require_once TREZOR_PATH . '/ModelTrezor.php';
        ModelTrezor::newInstance()->uninstall();
    }

    function trezor_update() {
        ModelTrezor::newInstance()->updateVersion();
    }

    function trezor_hidden_challenge() {
        if(function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(32));
        }
        $str = '';
        for($i=0;$i<64;$i++) {
            $str .= dechex(mt_rand(0, 15));
        }
        return $str;
    }

    osc_add_hook('trezor_button', 'trezor_button');
    function trezor_button($hook = 'trezor', $admin = false) {
        $trezor_challenge_hidden = trezor_hidden_challenge();
        $trezor_challenge_visual = date('Y-m-d H:i:s');
        if($hook!='trezor_link') {
            $hook = 'trezor';
        }
        if(($hook=='trezor' && !osc_is_web_user_logged_in()) || $hook=='trezor_link') {
            ?>
            <div id="trezor_button">
                <?php if($hook=="trezor_link") {
                    trezor_link_script($admin);
                } else {
                    trezor_login_script($admin);
                };?>
                <trezor:login callback="trezorLogin" challenge_hidden="<?php echo $trezor_challenge_hidden; ?>" challenge_visual="<?php echo $trezor_challenge_visual; ?>" icon="<?php echo trezor_logo(); ?>">
                </trezor:login>


                <script type="text/javascript">
                    window.connect_data = {};

                    var elements = document.getElementsByTagName('trezor:login');
                    var connect_origin = 'https://trezor.github.io';
                    var connect_path = connect_origin + '/connect/';

                    var content_css = '<style type="text/css">@import url("'+connect_path+'button.css")</style>';

                    var content_html = '<div id="trezorconnect-wrapper"><a id="trezorconnect-button" onclick="trezor_login_handler(\'@callback@\', \'@hosticon@\', \'@challenge_hidden@\', \'@challenge_visual@\');"><span id="trezorconnect-icon"></span><?php printf(__('Sign in with %s', 'trezor'), '<strong>TREZOR</strong>'); ?></a><span id="trezorconnect-info"><a id="trezorconnect-infolink" href="https://www.buytrezor.com/<?php echo osc_get_preference('affiliate_code', 'trezor')!=''?'?a='.osc_get_preference('affiliate_code', 'trezor'):''; ?>" target="_blank"><?php _e('What is TREZOR?', 'trezor'); ?></a></div>';

                    for (var i = 0; i < elements.length; i++) {
                        var e = elements[i];
                        callback = e.getAttribute('callback') || '';
                        hosticon = e.getAttribute('icon') || '';
                        challenge_hidden = e.getAttribute('challenge_hidden') || '';
                        challenge_visual = e.getAttribute('challenge_visual') || '';
                        e.parentNode.innerHTML = content_css + content_html.replace('@callback@', callback).replace('@hosticon@', hosticon).replace('@challenge_hidden@', challenge_hidden).replace('@challenge_visual@', challenge_visual);
                    }

                    function receiveMessage(event) {
                        if (event.origin !== connect_origin) return;
                        if (window.connect_data.interval) {
                            clearInterval(window.connect_data.interval);
                        }
                        if (window.connect_data.callback) {
                            window[window.connect_data.callback](event.data);
                        }
                    }

                    window.addEventListener('message', receiveMessage, false);

                    function trezor_login_handler(callback, hosticon, challenge_hidden, challenge_visual) {
                        var w = 500, h = 400, x = (screen.width - w) / 2, y = (screen.height - h) / 3;
                        var popup = window.open(connect_path + 'login.html', 'trezor_login_window', 'height='+h+',width='+w+',left='+x+',top='+y+',menubar=no,toolbar=no,location=no,personalbar=no,status=no');
                        window.connect_data.callback = callback;
                        // repeatedly sent request
                        window.connect_data.interval = setInterval(function() {
                            var request = {};
                            request.trezor_login = true;
                            request.icon = hosticon || 'https://trezor.github.io/connect/trezor.png';
                            request.challenge_hidden = challenge_hidden || Array.apply(null, Array(64)).map(function () {return Math.floor(Math.random()*16).toString(16);}).join('');
                            request.challenge_visual = challenge_visual || new Date().toISOString().substring(0,19).replace('T',' ');
                            popup.postMessage(request, connect_origin);
                        }, 250);
                    }

                </script>
            </div>
        <?php
            return array($trezor_challenge_hidden, $trezor_challenge_visual);
        };
    }

    function trezor_sha256($data) {
        return hash('sha256', $data, true);
    }

    function trezor_ajax() {
        osc_csrf_check();
        $challenge_hidden = Params::getParam('challenge_hidden');
        $challenge_visual = Params::getParam('challenge_visual');
        $public_key = strtolower(Params::getParam('public_key'));
        $signature = strtolower(Params::getParam('signature'));
        $version = Params::getParam('version');
        $admin = Params::getParam('admin')=='1'?1:0;

        if ($version == 1) {
            $message = hex2bin($challenge_hidden) . $challenge_visual;
        } elseif ($version == 2) {
            $message = trezor_sha256(hex2bin($challenge_hidden)) . trezor_sha256($challenge_visual);
        } else {
            die('Unknown version');
        }

        $R = substr($signature, 2, 64);
        $S = substr($signature, 66, 64);

        $ecdsa = new BitcoinECDSA();
        $hash = $ecdsa->hash256("\x18Bitcoin Signed Message:\n" . $ecdsa->numToVarIntString(strlen($message)) . $message);

        $success = (bool)$ecdsa->checkSignaturePoints($public_key, $R, $S, $hash);

        if($success) {
            $address = $ecdsa->getAddress($public_key);
            $user = ModelTrezor::newInstance()->findByAddress($address, $admin);
            if(isset($user['fk_i_user_id'])) {
                if($admin===1) {
                    $user = Admin::newInstance()->findByPrimaryKey($user['fk_i_user_id']);
                    if( !$user ) {
                        echo json_encode(array('success' => false, 'error' => __("The user doesn't exist", 'trezor')));
                        die;
                    }

                    Session::newInstance()->_set('adminId', $user['pk_i_id']);
                    Session::newInstance()->_set('adminUserName', $user['s_username']);
                    Session::newInstance()->_set('adminName', $user['s_name']);
                    Session::newInstance()->_set('adminEmail', $user['s_email']);
                    Session::newInstance()->_set('adminLocale', Params::getParam('locale'));

                    echo json_encode(array('success' => true, 'error' => __('The user has been signed in correctly.', 'trezor')));
                    die;

                } else {
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
            }
            echo json_encode(array('success' => false, 'error' => __('Unlinked TREZOR device. Please login into your account and go to TREZOR device section to link it.', 'trezor')));
        } else {
            echo json_encode(array('success' => false, 'error' => __('Wrong signature', 'trezor')));
        }
        die;

    }
    osc_add_hook('ajax_trezor', 'trezor_ajax');

    function trezor_ajax_link() {
        osc_csrf_check();
        $challenge_hidden = Params::getParam('challenge_hidden');
        $challenge_visual = Params::getParam('challenge_visual');
        $public_key = strtolower(Params::getParam('public_key'));
        $signature = strtolower(Params::getParam('signature'));
        $version = Params::getParam('version');
        $admin = Params::getParam('admin')=='1'?1:0;

        if ($version == 1) {
            $message = hex2bin($challenge_hidden) . $challenge_visual;
        } elseif ($version == 2) {
            $message = trezor_sha256(hex2bin($challenge_hidden)) . trezor_sha256($challenge_visual);
        } else {
            die('Unknown version');
        }

        $R = substr($signature, 2, 64);
        $S = substr($signature, 66, 64);

        $ecdsa = new BitcoinECDSA();
        $hash = $ecdsa->hash256("\x18Bitcoin Signed Message:\n" . $ecdsa->numToVarIntString(strlen($message)) . $message);

        try {
            $success = (bool)$ecdsa->checkSignaturePoints($public_key, $R, $S, $hash);
        } catch(Exception $e) {
            echo json_encode(array('success' => false, 'error' => $e->getMessage()));
            die;
        }

        if($success) {
            $address = $ecdsa->getAddress($public_key);
            $user = ModelTrezor::newInstance()->findByAddress($address, $admin);
            if(!isset($user['fk_i_user_id']) && (($admin===1 && osc_is_admin_user_logged_in()) || ($admin===0 && osc_is_web_user_logged_in()))) {
                if ($admin === 1) {
                    $user = Admin::newInstance()->findByPrimaryKey(osc_logged_admin_id());
                } else {
                    $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
                }
                if ( !osc_verify_password(Params::getParam('x'), (isset($user['s_password'])?$user['s_password']:'') )) {
                    echo json_encode(array('success' => false, 'error' => __('Wrong password', 'trezor')));
                    die;
                } else {
                    ModelTrezor::newInstance()->insert(
                        array(
                            'fk_i_user_id' => $user['pk_i_id'],
                            's_address' => $address,
                            'b_admin' => $admin
                        )
                    );
                    echo json_encode(array('success' => true, 'error' => __('Account linked correctly', 'trezor')));
                    die;

                }
            }
            echo json_encode(array('success' => false, 'error' => __('Address already linked to an user', 'trezor')));
        } else {
            echo json_encode(array('success' => false, 'error' => __('Wrong signature', 'trezor')));
        }
        die;

    }
    osc_add_hook('ajax_trezor_link', 'trezor_ajax_link');

    function trezor_user_menu($options) {
        $options[] = array('name' => __('TREZOR device'), 'url' => osc_route_url('trezor-manage'), 'class' => 'opt_trezor');
        return $options;
    }
    osc_add_hook('user_menu_filter', 'trezor_user_menu');

    function trezor_admin_menu() {
        osc_add_admin_submenu_divider('plugins', 'TREZOR plugin', 'trezor_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Help - How to use', 'trezor'), osc_route_admin_url('trezor-admin-help'), 'trezor_help', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Configure TREZOR login', 'trezor'), osc_route_admin_url('trezor-admin-conf'), 'trezor_conf', 'administrator');
    }
    osc_add_hook('admin_menu_init', 'trezor_admin_menu');

    function trezor_admin_manage($admin) {
        if(@$admin['pk_i_id']==osc_logged_admin_id()) {
            include TREZOR_PATH . 'views/admin/manage.php';
        }
    }
    osc_add_hook('admin_profile_form', 'trezor_admin_manage');

    function trezor_actions_admin() {
        switch( Params::getParam('action_specific') ) {
            case('trezor_settings'):
                osc_set_preference('affiliate_code', trim(Params::getParam('affiliate')), 'trezor');
                osc_add_flash_ok_message(__('Changes saved correctly', 'trezor'), 'admin');
                osc_redirect_to(osc_route_admin_url('trezor-conf'));
                break;
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
        return osc_base_url() . 'oc-content/plugins/trezor/img/logo.png';
    }

    function trezor_login_script($admin = false) {
        list($csrfname, $csrftoken) = osc_csrfguard_generate_token();
        ?>
        <script type="text/javascript">
            function trezorLogin(response) {
                if (response.success) {
                    $.post(
                        '<?php echo osc_base_url(true); ?>',
                        {
                            page: 'ajax',
                            action: 'runhook',
                            hook: 'trezor',
                            challenge_hidden: response.challenge_hidden,
                            challenge_visual: response.challenge_visual,
                            version: response.version,
                            public_key: response.public_key,
                            <?php if($admin) { echo "admin: '1',"; }; ?>
                            signature: response.signature,
                            CSRFName: '<?php echo $csrfname; ?>',
                            CSRFToken: '<?php echo $csrftoken?>'
                        },
                        function (response) {
                            if (response.success) {
                                <?php if($admin) {
                                    echo 'window.location = "' . osc_admin_base_url() . '";';
                                } else {
                                    echo 'window.location.reload(false);';
                                }?>
                            } else if (response.error == 1) {
                                alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.msg);
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

    <?php }

    function trezor_link_script($admin = false) {
        list($csrfname, $csrftoken) = osc_csrfguard_generate_token();
        ?>
        <div id="dialog-trezor" style="display: none;">
            <div class="form-horizontal" >
                <div class="form-row">
                    <?php _e('Please re-type your password', 'trezor');?>
                    <input type="password" name="trezor_pass" id="trezor_pass" value="" />
                </div>
                <div class="form-actions">
                    <div class="wrapper">
                        <a class="btn" href="javascript:void(0);" onclick="trezorCancel();"><?php _e('Cancel', 'trezor'); ?></a>
                        <a class="btn" href="javascript:void(0);" onclick="trezorContinue();"><?php _e('Continue', 'trezor'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="dialog-trezor-wait" style="display: none;">
            <div class="form-horizontal">
                <div class="form-row">
                    <?php _e('Please wait', 'trezor');?>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var trezorlink = new Object();
            trezorlink.page = 'ajax';
            trezorlink.action = 'runhook';
            trezorlink.hook = 'trezor_link';
            <?php if($admin) { echo "trezorlink.admin = '1';"; }; ?>
            trezorlink.CSRFName = '<?php echo $csrfname; ?>';
            trezorlink.CSRFToken = '<?php echo $csrftoken?>';
            $("#dialog-trezor").dialog({
                autoOpen: false,
                modal: true,
                <?php if($admin) { echo 'minHeight: 200,'; }; ?>
                title: '<?php echo osc_esc_js( __('Input your password', 'trezor') ); ?>'
            });
            $("#dialog-trezor-wait").dialog({
                autoOpen: false,
                modal: true,
                <?php if($admin) { echo 'minHeight: 200,'; }; ?>
                title: '<?php echo osc_esc_js( __('Loading data', 'trezor') ); ?>'
            });
            function trezorLogin(response) {
                if (response.success) {
                    trezorlink.challenge_hidden = response.challenge_hidden;
                    trezorlink.challenge_visual = response.challenge_visual;
                    trezorlink.public_key = response.public_key;
                    trezorlink.version = response.version;
                    trezorlink.signature = response.signature;
                    $("#dialog-trezor").dialog('open');
                } else {
                    alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.error);
                }
            }
            function trezorCancel() {
                $("#dialog-trezor").dialog('close');
                alert('<?php _e('You need to input your password to continue', 'trezor'); ?>');
            }
            function trezorContinue() {
                trezorlink.x = $("#trezor_pass").attr("value");
                $("#dialog-trezor").dialog('close');
                $("#dialog-trezor-wait").dialog('open');
                $.post(
                    '<?php echo osc_base_url(true); ?>',
                    trezorlink,
                    function (response) {
                        $("#dialog-trezor-wait").dialog('close');
                        if (response.success) {
                            window.location.reload(false);
                        } else if (response.error == 1) {
                            alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.msg);
                        } else {
                            alert('<?php _e('Failure:', 'trezor'); ?>\n\n' + response.error);
                        }
                    },
                    'json'
                );
            }
        </script>


    <?php }

    function trezor_admin_login() {
        echo '<div style="display:none;">';
        list($trezor_challenge_hidden, $trezor_challenge_visual) = trezor_button('login', true);
        echo '</div>';
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#loginform").after('<div style="float:right;" id="trezor_button"><trezor:login callback="trezorLogin" challenge_hidden="<?php echo $trezor_challenge_hidden; ?>" challenge_visual="<?php echo $trezor_challenge_visual; ?>" icon="<?php echo trezor_logo(); ?>"></trezor:login></div>');
                for (var i = 0; i < elements.length; i++) {
                    var e = elements[i];
                    callback = e.getAttribute('callback') || '';
                    hosticon = e.getAttribute('icon') || '';
                    challenge_hidden = e.getAttribute('challenge_hidden') || '';
                    challenge_visual = e.getAttribute('challenge_visual') || '';
                    e.parentNode.innerHTML = content_css + content_html.replace('@callback@', callback).replace('@hosticon@', hosticon).replace('@challenge_hidden@', challenge_hidden).replace('@challenge_visual@', challenge_visual);
                }
            });
        </script>

        <?php

    }
    osc_add_hook('login_admin_form', 'trezor_admin_login');

    osc_add_route('trezor-admin-help', 'trezor/admin/help', 'trezor/admin/help', 'trezor/views/admin/help.php');
    osc_add_route('trezor-admin-conf', 'trezor/admin/conf', 'trezor/admin/conf', 'trezor/views/admin/conf.php');
    osc_add_route('trezor-manage', 'trezor/manage', 'trezor/manage', 'trezor/views/user/manage.php', true);

    osc_register_plugin(osc_plugin_path(TREZOR_PATH . 'index.php'), 'trezor_install');
    osc_add_hook(osc_plugin_path(TREZOR_PATH . 'index.php')."_uninstall", 'trezor_uninstall');
    osc_add_hook(osc_plugin_path(TREZOR_PATH . 'index.php')."_enable", 'trezor_update');
    osc_enqueue_script('jquery-ui');

