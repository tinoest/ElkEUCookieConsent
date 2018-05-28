<?php

// integrate_init_theme
function int_init_theme_eu_cookie_consent()
{
	global $context;

        // EU cookie mod
	$context['html_headers'] .= '
		<script type="text/javascript">
		function getCookie(name) {
	    		var v = document.cookie.match(\'(^|;) ?\' + name + \'=([^;]*)(;|$)\');
	    		return v ? v[2] : null;
		}
		function setCookie(name, value, days) {
	    		var d = new Date;
			d.setDate(d.getDate() + days);
	    		document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
		}
		function deleteCookie(name) { 
			setCookie(name, \'\', -1); 
		}

		window.onload = function() {
			var cookieSet = getCookie("eu_cookie_consent");
			if(cookieSet === null) {
				var x = document.getElementById(\'eu_cookie_message\');
				x.style.display = "block";
			}
		}

	</script>';

	$context['html_headers'] .= '
	<style>
		#eu_cookie_message
		{
			position: fixed;
			padding: 1em;
			width: 100vw;
			bottom: 20px;
			text-align: center;
			display: none;
        	}
		#eu_cookie_notice
		{
			display: inline-block;
			margin: 0 auto;
			padding: 10px;
			border-radius: 5px;
			font-size: 12px;
		}
		#eu_cookie_button
		{
			background: #346;
			color: #fff;
			font: bold 11px arial;
			padding: 3px 12px;
			border-radius: 3px;
		}
	</style>';

	$context['insert_after_template'] = '
	<div id="eu_cookie_message">
		<div id="eu_cookie_notice" style="background: #000; background-color: rgba(0,0,0,0.80); color: #fff;">	
			This website uses cookies to ensure you get the best experience on our website &nbsp;&nbsp;
			<button id="eu_cookie_button" type="button">OK</button>
		</div>
	</div>

	<script type="text/javascript">
		document.getElementById(\'eu_cookie_button\').onclick = function() {
			setCookie("eu_cookie_consent", 1, 30);
			var x = document.getElementById(\'eu_cookie_message\');
			x.style.display = "none";
		}
	</script>';

}

// This doesn't actually do anything with the buffer it just unsets the session if not allowed
function int_buffer_eu_cookie_consent( $buffer )
{
	global $user_info;

	if($user_info['is_guest'] && !array_key_exists('eu_cookie_consent', $_COOKIE)) {
		unset($_COOKIE['PHPSESSID']);
		setcookie('PHPSESSID', null, -1, '/');
	}

	return $buffer;
}

?>
