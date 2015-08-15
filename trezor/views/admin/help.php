<div style="padding: 20px;">
    <h2 class="render-title"><?php _e('What does this plugin?', 'trezor'); ?></h2>

    <p><?php _e('This plugin will show a button to perform login into your website with a Trezor device. Important: You need a <a href="https://buytrezor.com?a=227f182fcbbe" >Trezor device</a>.', 'trezor'); ?></p>

    <h2 class="render-title"><?php _e('How to use Trezor connect', 'trezor'); ?></h2>

    <p><?php _e('You need to modify your theme to be able to include the button to let your users log in. Login buttons are usually located at header.php and at user-login.php.', 'trezor'); ?></p>
    <p><?php _e('Please add the following to your theme in the place you want to show the button:', 'trezor'); ?></p>

    <pre>
&lt;?php osc_run_hook('trezor_button'); ?&gt;
    </pre>

    <p><?php _e('It will show as follows:', 'trezor'); ?></p>

    <a style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; display: block; padding: 6px 12px; margin-bottom: 0; font-weight: normal; line-height: 1.42857143; text-align: center; white-space: nowrap; vertical-align: middle; cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; border: 1px solid transparent; border-radius: 4px; text-decoration: none; position:relative; padding-left:44px; width:136px; color:#fff; background-color:#59983b; border-color:rgba(0,0,0,0.2);" onmouseover="this.style.background='#43732d';" onmouseout="this.style.background='#59983b';" ><span style="position:absolute; left:0; top:0; bottom:0; width:32px; line-height:34px; font-size:1.6em; text-align:center; border-right:1px solid rgba(0,0,0,0.2); background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAABWklEQVRYw+2WPUsDQRCGZ0w0RRQUCwUllT9AFAQra/+BjYiQRgRBBFv/goWFlVjaBm1SCVpZiIKgha1iI4iFXxDz2MzBEe6SveOWIOwLx3I7X+/NzuyNSEBADgDrQAN4Al6AU2ADKPkOPAk0ScclUPNJIB78ETgEDoD72P6Vl0xY2iMcA5WYrGxEImz7INCIfXklQV4CbkznwtXvQAYO87Y2VfWnU6iqvyJyZq+zrseQhcCUrc9ddCLZiD2FEuhbzy92FJgrjoClXv7VgcCriIzn5P+hqsPdFMoOTgZT9r9E5NOOsSoiQzn998zAeyylC0AV0JQ2HAOWgVuz+S6SwEoGmxNXAn3vgkAgEPgXV3HUhg/ALjDdRXcO2AfefNwDEdrAOVAHRm1M2wHuEv4HhRBYtVGslRQgZb9lNmtFD6RbNvMloW1D6SYw4bsuZoA9q4trq42aBATkwB8MsRcDkn7CNQAAAABJRU5ErkJggg==') no-repeat;"></span><?php printf(__('Sign in with %s', 'trezor'), '<strong>TREZOR</strong>'); ?></a><span style="display: block;font-family: Helvetica, Arial, sans-serif; font-size: 9px; width: 192px; text-align: right; margin-top: 2px;"><a href="https://www.bitcointrezor.com/" target="_blank" style="text-decoration: none; color: #59983b;"><?php _e('What is TREZOR?', 'trezor'); ?></a></span>

    <br>
    <br>

    <p><?php _e('Button will be created inside a &lt;div id="trezor_button"&gt;&lt;/div&gt;, you may want to add some rules in your stylesheets to place it correctly in your theme.', 'trezor'); ?></p>
</div>