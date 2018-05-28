<?php

// integrate_init_theme
function int_init_theme_EUCookieConsent()
{
	global $context, $txt, $modSettings;

	// If its not enabled then there is no point in continuing	
	if(empty($modSettings['eucookieconsent_enabled'])) 
		return;

	loadLanguage('EUCookieConsent');

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

	$eucookieconsent_txt = empty($modSettings['eucookieconsent_custom_text']) ? $txt['eucookieconsent_string'] : $modSettings['eucookieconsent_custom_text'];
	$eucookieconsent_url = empty($modSettings['eucookieconsent_custom_url']) ? '' : '<a style="color: #fff;" href="'.$modSettings['eucookieconsent_custom_url'].'">'.$txt['eucookieconsent_more_info'].'</a>';

	$context['insert_after_template'] = '
	<div id="eu_cookie_message">
		<div id="eu_cookie_notice" style="background: #000; background-color: rgba(0,0,0,0.80); color: #fff;">
			'.$eucookieconsent_txt.'
			'.$eucookieconsent_url.'
			<button id="eu_cookie_button" type="button">'.$txt['eucookieconsent_ok'].'</button>
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
function int_buffer_EUCookieConsent( $buffer )
{
	global $user_info, $modSettings;

	// If its not enabled then there is no point in continuing	
	if(empty($modSettings['eucookieconsent_enabled'])) 
		return $buffer;

	if($user_info['is_guest'] && !empty($modSettings['eucookieconsent_session']) && !array_key_exists('eu_cookie_consent', $_COOKIE)) {
		unset($_COOKIE['PHPSESSID']);
		setcookie('PHPSESSID', null, -1, '/');
	}

	return $buffer;
}


/**
 * int_adminAreasEUCookieConsent()
 *
 * - Admin Hook, integrate_admin_areas, called from Admin.php
 * - Used to add/modify admin menu areas
 *
 * @param mixed[] $admin_areas
 */
function int_adminAreasEUCookieConsent(&$admin_areas)
{
	global $txt;
	loadLanguage('EUCookieConsent');
	$admin_areas['config']['areas']['addonsettings']['subsections']['eucookieconsent'] = array($txt['eucookieconsent_title']);
}

/**
 * int_adminEUCookieConsent()
 *
 * - Admin Hook, integrate_sa_modify_modifications, called from AddonSettings.controller.php
 * - Used to add subactions to the addon area
 *
 * @param mixed[] $sub_actions
 */
function int_adminEUCookieConsent(&$sub_actions)
{
	global $context, $txt;
	$sub_actions['eucookieconsent'] = array(
		'dir' => SOURCEDIR,
		'file' => 'EUCookieConsent.integration.php',
		'function' => 'eucookieconsent_settings',
		'permission' => 'admin_forum',
	);
	$context[$context['admin_menu_name']]['tab_data']['tabs']['eucookieconsent']['description'] = $txt['eucookieconsent_desc'];
}
/**
 * eucookieconsent_settings()
 *
 * - Defines our settings array and uses our settings class to manage the data
 */
function eucookieconsent_settings()
{
	global $txt, $context, $scripturl, $modSettings;
	loadLanguage('EUCookieConsent');
	// Lets build a settings form
	require_once(SUBSDIR . '/SettingsForm.class.php');
	// Instantiate the form
	$euCookieConsentSettings = new Settings_Form();
	// All the options, well at least some of them!
	$config_vars = array(
		array('check',	'eucookieconsent_enabled', 'postinput' => $txt['eucookieconsent_enabled_desc']),
		array('title',	'eucookieconsent_options'),
		array('check',	'eucookieconsent_session'),
		array('text', 	'eucookieconsent_custom_text'),
		array('text', 	'eucookieconsent_custom_url'),
	);
	// Load the settings to the form class
	$euCookieConsentSettings->settings($config_vars);
	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		Settings_Form::save_db($config_vars);
		redirectexit('action=admin;area=addonsettings;sa=eucookieconsent');
	}
	// Continue on to the settings template
	$context['settings_title'] = $txt['eucookieconsent_title'];
	$context['page_title'] = $context['settings_title'] = $txt['eucookieconsent_settings'];
	$context['post_url'] = $scripturl . '?action=admin;area=addonsettings;sa=eucookieconsent;save';
	Settings_Form::prepare_db($config_vars);
}

?>
