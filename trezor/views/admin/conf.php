<?php if ( (!defined('ABS_PATH')) ) exit('ABS_PATH is not loaded. Direct access is not allowed.');
if ( !OC_ADMIN ) exit('User access is not allowed.');

$url = parse_url(osc_base_url());
$origin = $url['scheme'] . '://' . $url['host'];

?>
<style type="text/css" media="screen">
    .command { background-color: white; color: #2E2E2E; border: 1px solid black; padding: 8px; }
    .theme-files { min-width: 500px; }
</style>
<h2 class="render-title"><?php _e('Trezor login information', 'trezor'); ?> <span style="font-size: 12px;"><a href="<?php echo osc_route_admin_url('trezor-admin-help'); ?>" ><?php _e('Help setting my login button', 'trezor'); ?></a></span></h2>
<?php $logo_prefence = osc_get_preference('logo', 'trezor'); ?>
<?php if( is_writable( osc_uploads_path()) ) { ?>
    <?php if($logo_prefence) { ?>
        <h3 class="render-title"><?php _e('Preview', 'trezor') ?></h3>
        <div id="preview_logo">

            <style type="text/css">
                body {
                    margin: 0;
                    padding: 0;
                }
                #main {
                    background: #eee;
                    color: #333;
                    font-family: Helvetica, Arial, sans-serif;
                    width: 500px;
                    height: 400px;
                }
                #top {
                    background: #333 8px 4px no-repeat url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAAAwCAYAAADZ9HK+AAAFiUlEQVR42u2ca4hVVRTHf8vU1Cl1fDuphaZMWVkGPbQsMagpoegBWUZfkgQjg6QQQ/JDRWjQA3uKEJb2ISGI0lTIV0K+sixQfBAGVmTqqONMjfrvg3vietr73nPu3Ma5d/YfDueevdfaj7XW2Wudtc+5EBEREREREdERYa1hlnQHMAw4Cewws11RpBUOSdMk7ZAfpyQtlTQsSqryFF8jabfSY3aUWuUof4CkM8qOL6P0KsMAjgcU3CRpo6RdeYxgSZRgeSt/q0epqyT1S9B1kTQ2YAR9oyTLU/n9PMpcWICnt6SjCZ69UZrlaQBzE4r8KSXfbR7D6RMlWn4GcDihxKkZeHcmeEdHibY/dMqjQAOqE8WrMrS9LnF9cxR3GRkA0M1T9keGtg8nrmMgWGYGENEB0DkLsZnpf445BgFrgCz9dDGzWknbgK556M64VekbYJ6ZNef0uzbDCmXAeOBd4KoMN9rLZvZxYr6jgNnAOGB4jj4OAFuAt8xsnUdOXwP9CvR5DPgBmG9m+zMbgJk1Skp2PAPo7WKD7k7gXZxw/waagXrgKDA2Oe40ygSKDRZHAxemoLsdmCNpvJltcmW1wMAMfV0AjMhgACQVJukdYHqAdpg7HpC0GrjXzBpz6muBQSn6HAdMl/SUmS1MexeOkLTERe7FpH/zYZ+kJyR1D/Q9tJhGHW9TEazdHO9vGfn6SNqckWem66uTpF+KGGvPHDn9WgT/9WljgN3A1JzlrpQYDnwArGgnLvCztnyqduePgCF53FRopdzayv43pTUAawNhhILPg0ANMNgd1R6azc4NtdDUBNr62fn1FppZHporA7wnnUsY7DlqnIur89T1cq7Qh7clXQFM8dRtA/q7+VYDH3poRkp6LND2CeDinHHc7aHpKqkmjQs4rf8f6zMEhklsCNA1FUo/e+Z2KOACGiT1klTtOfq4HIlvDN8G5nu1q3/VU7c80Nazobl7XMAxD/8yD39tMU8BG4DvXWR6yAV5DcAp4LQ7d3YBXBegCujpLPpS4FrgpvOwvHeTdKMbU0tAlFx5mgK8PdxdHsJFTga5An8JuMFD+6SZ7XS/H/bUTwsE4a9JWpAoviUUlEq6zskeYCgw2UPXkHUF+KpEj3erz8MKkAariwwCqxJ9j0mz0nnqjxSY+9pAwFtMENgcE0H/xUMlMO5qYIen6nczm5AyMMyXcygVJkYDOBd3mtnRErSzPVA+0lN2IHFdXeBdiQklmus8M9sYDeBc5efb2Grg7F5Ile8wswZ3978OXObhf9zMjnvKP/GUvR9YWZ7xFG8sYq7LzOzFkqSCywx7zGyUpEkuvZyLBcA1BVLkk0M3iKTPgQeBmYEV4bCkpHvZDywGnkuU3+9Su7OB71yu5AXgEU/b74XSvmbWy42tOaHX+7L6tEoJAvfm1PmCvLoC9YUygdsz8ix2fS0t8tH5xzyZwGO5+wQe3vlZEzKVhhmeslda2eapjPQtUfijwJ6MvPXArSlpfY9/syT16MgG8IWnbIykyW09EDOTmY0CVpJug2wfUG1mR1K23wDs9FQ9XykGoJTlyhFKEzDHw/OGO58p0RiyGEIdZ1PHiwIkK4AaM7vcsw1f6NqX9p4raUDZxQAdBS713N+lm62t+u0cRd8+YGb156PftnIBiipunyhkAAMlDWnl0jYEuCSKunx80enAZ9/T0/omSSZpqqTG1m4HR7QPA/h3R0nSgdAHIpImSdor6a9SvQ8Q0fYGkBYnJa2XNEXSckl/ZuDdEiXdfg2gr6SnA18FtwYNkhZJmhilXF7Ppm+6t3mLwQlJayTdE6VZ/sbQX9KnkuoLKL3R/WFEXZRaZRqCSRokaWVC8Qcl3SUpfm7WgYyhSlJt/AeQiIiIiIiIiIjywj96I9wHYT5hWgAAAABJRU5ErkJggg==');
                    color: #fff;
                    height: 56px;
                    line-height: 56px;
                    padding-right: 12px;
                    text-align: right;
                    font-size: 30px;
                }
                #middle {
                    font-size: 21px;
                    text-align: center;
                    height: 226px;
                    padding-top: 70px;
                }
                #bottom {
                    background: #333;
                    color: #fff;
                    height: 48px;
                    line-height: 48px;
                    font-size: 16px;
                    text-align: center;
                }
                #login_icon {
                    padding-bottom: 16px;
                }
                #passphrase_dialog {
                    width: 250px;
                    height: 200px;
                    background: #eee;
                    position: absolute;
                    border: none;
                    left: 125px;
                    top: 107px;
                    display: none;
                }
                #passphrase_header {
                    font-size: 18px;
                    text-align: center;
                }
                #passphrase_subheader {
                    font-size: 75%;
                    margin-top: 20px;
                    margin-bottom: 20px;
                    text-align: center;
                }
                #passphrase_input_row {
                    margin-top: 10px;
                    margin-bottom: 10px;
                    width: 100%;
                }
                #passphrase_input_row input {
                    width: 100%;
                    padding-left: 6px;
                    padding-right: 6px;
                    box-sizing: border-box;
                    padding-top: 4px;
                    padding-bottom: 4px;
                    text-align: center;
                }
                #passphrase_show_label {
                    font-size: 90%;
                    top: -2px;
                    position: relative;
                }
                #passphrase_show_row {
                    margin-bottom: 15px;
                    text-align: center;
                }
                #passphrase_enter button {
                    width: 100%;
                    height: 25px;
                    background-color: rgb(123, 123, 123);
                    border: 0px;
                    color: white;
                    font-size: 1em;
                }
                #passphrase_enter button:hover {
                    background-color: rgb(100, 100, 100);
                    cursor: pointer;
                }
                #passphrase_enter button:active {
                    box-shadow: inset 0 4px 5px rgba(0,0,0,.125);
                }
                #passphrase_enter button:focus {
                    outline: 0;
                }
                #pin_dialog {
                    width: 500px;
                    height: 285px;
                    background: #eee;
                    position: absolute;
                    left: 0px;
                    top: 63px;
                    display: none;
                    border: none;
                }
                #pin_header {
                    font-size: 18px;
                    text-align: center;
                    margin-top: -5px;
                }
                #pin_subheader {
                    margin-top: 4px;
                    font-size: 75%;
                    position: relative;
                    text-align: center;
                }
                #pin_table {
                    display: table;
                    margin-top: 6px;
                    margin-left: 160px;
                }
                #pin_table > div {
                    display: table-row;
                }
                #pin_table button {
                    width: 55px;
                    height: 55px;
                    border: 1px solid transparent;
                    cursor: pointer;
                    font-weight: 400;
                    font-size: 18px;
                    display: table-cell;
                    margin-right: 6px;
                    margin-bottom: 6px;
                    background-color: rgb(189, 189, 189);
                }
                #pin_table button:focus {
                    outline: 0;
                }
                #pin_table button:active {
                    box-shadow: inset 0 2px 3px rgba(0,0,0,.125);
                }
                #pin_backspace {
                    position: absolute;
                    right: 0px;
                    top: 0px;
                    border: 1px solid transparent;
                    cursor: pointer;
                    line-height: 1.33;
                    background-color: rgb(189, 189, 189);
                    padding: 0px;
                    font-size: 15px;
                    padding-right: 3px;
                }
                #pin_backspace:focus {
                    outline: 0;
                }
                #pin_backspace:active {
                    box-shadow: inset 0 2px 3px rgba(0,0,0,.125);
                }
                #pin_back_arrow {
                    position: absolute;
                    left: -12px;
                    top: -1px;
                    border-top: 11px solid transparent;
                    border-bottom: 11px solid transparent;
                    border-right: 11px solid  rgb(189, 189, 189);
                }
                #pin_input_row {
                    width: 177px;
                    position: relative;
                    margin-bottom: 2px;
                    margin-left: 160px;
                }
                #pin_input_row input {
                    border: none;
                    background-color: rgb(238, 238, 238);
                    font-size: 1.2em;
                    width: 140px;
                    text-align: center;
                    padding-left: 19px;
                }
                #pin_enter {
                    width: 177px;
                    margin-left: 160px;
                    margin-top: 5px;
                }
                #pin_enter button {
                    width: 100%;
                    height: 25px;
                    background-color: rgb(123, 123, 123);
                    border: 0px;
                    color: white;
                    font-size: 1em;
                }
                #pin_enter button:hover {
                    background-color: rgb(100, 100, 100);
                    cursor: pointer;
                }
                #pin_enter button:active {
                    box-shadow: inset 0 4px 5px rgba(0,0,0,.125);
                }
                #pin_enter button:focus {
                    outline: 0;
                }
            </style>
            <div id="main">
                <div id="top">Sign in</div>
                <div id="middle">
                    <img id="login_icon" width="128" height="128" src="<?php echo trezor_logo() .'?'.time();?>" >
                    <br>
                    <strong id="login_origin"><?php echo $origin; ?></strong>
                </div>
                <div id="bottom">
                    <span id="status_bar">Please connect your TREZOR device ...</span>
                </div>
            </div>


        </div>

        <form action="<?php echo osc_route_admin_url('trezor-conf'); ?>" method="post" enctype="multipart/form-data" class="nocsrf">
            <input type="hidden" name="action_specific" value="trezor_remove" />
            <fieldset>
                <div class="form-horizontal">
                    <div class="form-actions">
                        <input id="button_remove" type="submit" value="<?php echo osc_esc_html(__('Remove logo','trezor')); ?>" class="btn btn-red">
                    </div>
                </div>
            </fieldset>
        </form>
    <?php } else { ?>
        <div class="flashmessage flashmessage-warning flashmessage-inline" style="display: block;">
            <p><?php _e('No logo has been uploaded yet', 'trezor'); ?></p>
        </div>
    <?php } ?>
    <h2 class="render-title separate-top"><?php _e('Upload logo', 'trezor') ?></h2>
    <p><?php _e('The preferred size of the logo is 128x128 (will be resized).', 'trezor'); ?></p>
    <?php if( $logo_prefence ) { ?>
        <div class="flashmessage flashmessage-inline flashmessage-warning"><p><?php _e('<strong>Note:</strong> Uploading another logo will overwrite the current logo.', 'trezor'); ?></p></div>
    <?php } ?>
    <br/><br/>
    <form action="<?php echo osc_route_admin_url('trezor-conf'); ?>" method="post" enctype="multipart/form-data" class="nocsrf">
        <input type="hidden" name="action_specific" value="trezor_upload_logo" />
        <fieldset>
            <div class="form-horizontal">
                <div class="form-row">
                    <div class="form-label"><?php _e('Logo image (png,gif,jpg)','trezor'); ?></div>
                    <div class="form-controls">
                        <input type="file" name="logo" id="package" />
                    </div>
                </div>
                <div class="form-actions">
                    <input id="button_save" type="submit" value="<?php echo osc_esc_html(__('Upload','trezor')); ?>" class="btn btn-submit">
                </div>
            </div>
        </fieldset>
    </form>
<?php } else { ?>
    <div class="flashmessage flashmessage-error" style="display: block;">
        <p>
            <?php
            $msg  = sprintf(__('The images folder <strong>%s</strong> is not writable on your server', 'trezor'), WebThemes::newInstance()->getCurrentThemePath() ."images/" ) .", ";
            $msg .= __("Osclass can't upload the logo image from the administration panel.", 'trezor') . ' ';
            $msg .= __('Please make the aforementioned image folder writable.', 'trezor') . ' ';
            echo $msg;
            ?>
        </p>
        <p>
            <?php _e('To make a directory writable under UNIX execute this command from the shell:','trezor'); ?>
        </p>
        <p class="command">
            chmod a+w <?php echo WebThemes::newInstance()->getCurrentThemePath() ."images/"; ?>
        </p>
    </div>
<?php } ?>
