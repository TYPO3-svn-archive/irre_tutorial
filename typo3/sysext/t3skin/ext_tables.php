<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE' || (TYPO3_MODE == 'FE' && isset($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->isFrontendEditingActive())) {
	global $TBE_STYLES;

		// register as a skin
	$TBE_STYLES['skins'][$_EXTKEY] = array(
		'name' => 't3skin',
	);

		// Support for other extensions to add own icons...
	$presetSkinImgs = is_array($TBE_STYLES['skinImg']) ?
		$TBE_STYLES['skinImg'] :
		array();

	$TBE_STYLES['skins'][$_EXTKEY]['stylesheetDirectories']['sprites'] = 'EXT:t3skin/stylesheets/sprites/';

	/**
	 * Setting up backend styles and colors
	 */
	$TBE_STYLES['mainColors'] = array(	// Always use #xxxxxx color definitions!
		'bgColor'    => '#FFFFFF',		// Light background color
		'bgColor2'   => '#FEFEFE',		// Steel-blue
		'bgColor3'   => '#F1F3F5',		// dok.color
		'bgColor4'   => '#E6E9EB',		// light tablerow background, brownish
		'bgColor5'   => '#F8F9FB',		// light tablerow background, greenish
		'bgColor6'   => '#E6E9EB',		// light tablerow background, yellowish, for section headers. Light.
		'hoverColor' => '#FF0000',
		'navFrameHL' => '#F8F9FB'
	);

	$TBE_STYLES['colorschemes'][0] = '-|class-main1,-|class-main2,-|class-main3,-|class-main4,-|class-main5';
	$TBE_STYLES['colorschemes'][1] = '-|class-main11,-|class-main12,-|class-main13,-|class-main14,-|class-main15';
	$TBE_STYLES['colorschemes'][2] = '-|class-main21,-|class-main22,-|class-main23,-|class-main24,-|class-main25';
	$TBE_STYLES['colorschemes'][3] = '-|class-main31,-|class-main32,-|class-main33,-|class-main34,-|class-main35';
	$TBE_STYLES['colorschemes'][4] = '-|class-main41,-|class-main42,-|class-main43,-|class-main44,-|class-main45';
	$TBE_STYLES['colorschemes'][5] = '-|class-main51,-|class-main52,-|class-main53,-|class-main54,-|class-main55';

	$TBE_STYLES['styleschemes'][0]['all'] = 'CLASS: formField';
	$TBE_STYLES['styleschemes'][1]['all'] = 'CLASS: formField1';
	$TBE_STYLES['styleschemes'][2]['all'] = 'CLASS: formField2';
	$TBE_STYLES['styleschemes'][3]['all'] = 'CLASS: formField3';
	$TBE_STYLES['styleschemes'][4]['all'] = 'CLASS: formField4';
	$TBE_STYLES['styleschemes'][5]['all'] = 'CLASS: formField5';

	$TBE_STYLES['styleschemes'][0]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][1]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][2]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][3]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][4]['check'] = 'CLASS: checkbox';
	$TBE_STYLES['styleschemes'][5]['check'] = 'CLASS: checkbox';

	$TBE_STYLES['styleschemes'][0]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][1]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][2]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][3]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][4]['radio'] = 'CLASS: radio';
	$TBE_STYLES['styleschemes'][5]['radio'] = 'CLASS: radio';

	$TBE_STYLES['styleschemes'][0]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][1]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][2]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][3]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][4]['select'] = 'CLASS: select';
	$TBE_STYLES['styleschemes'][5]['select'] = 'CLASS: select';

	$TBE_STYLES['borderschemes'][0] = array('', '', '', 'wrapperTable');
	$TBE_STYLES['borderschemes'][1] = array('', '', '', 'wrapperTable1');
	$TBE_STYLES['borderschemes'][2] = array('', '', '', 'wrapperTable2');
	$TBE_STYLES['borderschemes'][3] = array('', '', '', 'wrapperTable3');
	$TBE_STYLES['borderschemes'][4] = array('', '', '', 'wrapperTable4');
	$TBE_STYLES['borderschemes'][5] = array('', '', '', 'wrapperTable5');



		// Setting the relative path to the extension in temp. variable:
	$temp_eP = t3lib_extMgm::extRelPath($_EXTKEY);

		// Alternative dimensions for frameset sizes:
	$TBE_STYLES['dims']['leftMenuFrameW'] = 190;		// Left menu frame width
	$TBE_STYLES['dims']['topFrameH']      = 42;			// Top frame height
	$TBE_STYLES['dims']['navFrameWidth']  = 280;		// Default navigation frame width

		// Setting roll-over background color for click menus:
		// Notice, this line uses the the 'scriptIDindex' feature to override another value in this array (namely $TBE_STYLES['mainColors']['bgColor5']), for a specific script "typo3/alt_clickmenu.php"
	$TBE_STYLES['scriptIDindex']['typo3/alt_clickmenu.php']['mainColors']['bgColor5'] = '#dedede';

		// Setting up auto detection of alternative icons:
	$TBE_STYLES['skinImgAutoCfg'] = array(
		'absDir'             => t3lib_extMgm::extPath($_EXTKEY).'icons/',
		'relDir'             => t3lib_extMgm::extRelPath($_EXTKEY).'icons/',
		'forceFileExtension' => 'gif',	// Force to look for PNG alternatives...
#		'scaleFactor'        => 2/3,	// Scaling factor, default is 1
		'iconSizeWidth'      => 16,
		'iconSizeHeight'     => 16,
	);

		// Changing icon for filemounts, needs to be done here as overwriting the original icon would also change the filelist tree's root icon
	$TCA['sys_filemounts']['ctrl']['iconfile'] = '_icon_ftp_2.gif';

		// Manual setting up of alternative icons. This is mainly for module icons which has a special prefix:
	$TBE_STYLES['skinImg'] = array_merge($presetSkinImgs, array (
		'gfx/ol/blank.gif'                         => array('clear.gif','width="18" height="16"'),
		'MOD:web/website.gif'                      => array($temp_eP.'icons/module_web.gif','width="24" height="24"'),
		'MOD:web_layout/layout.gif'                => array($temp_eP.'icons/module_web_layout.gif','width="24" height="24"'),
		'MOD:web_view/view.gif'                    => array($temp_eP.'icons/module_web_view.png','width="24" height="24"'),
		'MOD:web_list/list.gif'                    => array($temp_eP.'icons/module_web_list.gif','width="24" height="24"'),
		'MOD:web_info/info.gif'                    => array($temp_eP.'icons/module_web_info.png','width="24" height="24"'),
		'MOD:web_perm/perm.gif'                    => array($temp_eP.'icons/module_web_perms.png','width="24" height="24"'),
		'MOD:web_func/func.gif'                    => array($temp_eP.'icons/module_web_func.png','width="24" height="24"'),
		'MOD:web_ts/ts1.gif'                       => array($temp_eP.'icons/module_web_ts.gif','width="24" height="24"'),
		'MOD:web_modules/modules.gif'              => array($temp_eP.'icons/module_web_modules.gif','width="24" height="24"'),
		'MOD:web_txversionM1/cm_icon.gif'          => array($temp_eP.'icons/module_web_version.gif','width="24" height="24"'),
		'MOD:file/file.gif'                        => array($temp_eP.'icons/module_file.gif','width="22" height="24"'),
		'MOD:file_list/list.gif'                   => array($temp_eP.'icons/module_file_list.gif','width="22" height="24"'),
		'MOD:file_images/images.gif'               => array($temp_eP.'icons/module_file_images.gif','width="22" height="22"'),
		'MOD:user/user.gif'                        => array($temp_eP.'icons/module_user.gif','width="22" height="22"'),
		'MOD:user_task/task.gif'                   => array($temp_eP.'icons/module_user_taskcenter.gif','width="22" height="22"'),
		'MOD:user_setup/setup.gif'                 => array($temp_eP.'icons/module_user_setup.gif','width="22" height="22"'),
		'MOD:user_doc/document.gif'                => array($temp_eP.'icons/module_doc.gif','width="22" height="22"'),
		'MOD:user_ws/sys_workspace.gif'            => array($temp_eP.'icons/module_user_ws.gif','width="22" height="22"'),
		'MOD:tools/tool.gif'                       => array($temp_eP.'icons/module_tools.gif','width="25" height="24"'),
		'MOD:tools_beuser/beuser.gif'              => array($temp_eP.'icons/module_tools_user.gif','width="24" height="24"'),
		'MOD:tools_em/em.gif'                      => array($temp_eP.'icons/module_tools_em.png','width="24" height="24"'),
		'MOD:tools_em/install.gif'                 => array($temp_eP.'icons/module_tools_em.gif','width="24" height="24"'),
		'MOD:tools_dbint/db.gif'                   => array($temp_eP.'icons/module_tools_dbint.gif','width="25" height="24"'),
		'MOD:tools_config/config.gif'              => array($temp_eP.'icons/module_tools_config.gif','width="24" height="24"'),
		'MOD:tools_install/install.gif'            => array($temp_eP.'icons/module_tools_install.gif','width="24" height="24"'),
		'MOD:tools_log/log.gif'                    => array($temp_eP.'icons/module_tools_log.gif','width="24" height="24"'),
		'MOD:tools_txphpmyadmin/thirdparty_db.gif' => array($temp_eP.'icons/module_tools_phpmyadmin.gif','width="24" height="24"'),
		'MOD:tools_isearch/isearch.gif'            => array($temp_eP.'icons/module_tools_isearch.gif','width="24" height="24"'),
		'MOD:help/help.gif'                        => array($temp_eP.'icons/module_help.gif','width="23" height="24"'),
		'MOD:help_about/info.gif'                  => array($temp_eP.'icons/module_help_about.gif','width="25" height="24"'),
		'MOD:help_aboutmodules/aboutmodules.gif'   => array($temp_eP.'icons/module_help_aboutmodules.gif','width="24" height="24"'),
		'MOD:help_cshmanual/about.gif'         => array($temp_eP.'icons/module_help_cshmanual.gif','width="25" height="24"'),
		'MOD:help_txtsconfighelpM1/moduleicon.gif' => array($temp_eP.'icons/module_help_ts.gif','width="25" height="24"'),
	));

		// Logo at login screen
	$TBE_STYLES['logo_login'] = $temp_eP . 'images/login/typo3logo-white-greyback.gif';

		// extJS theme
	$TBE_STYLES['extJS']['theme'] =  $temp_eP . 'extjs/xtheme-t3skin.css';

	// Adding HTML template for login screen
	$TBE_STYLES['htmlTemplates']['templates/login.html'] = 'sysext/t3skin/templates/login.html';

	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('t3skin').'registerIe6Stylesheet.php';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preHeaderRenderHook'][] = t3lib_extMgm::extPath('t3skin').'pngfix/class.tx_templatehook.php:tx_templatehook->registerPngFix';

	t3lib_SpriteManager::addIconSprite(
		array(
			'flags-ad',
			'flags-ad-overlay',
			'flags-ae',
			'flags-ae-overlay',
			'flags-af',
			'flags-af-overlay',
			'flags-ag',
			'flags-ag-overlay',
			'flags-ai',
			'flags-ai-overlay',
			'flags-al',
			'flags-al-overlay',
			'flags-am',
			'flags-am-overlay',
			'flags-an',
			'flags-an-overlay',
			'flags-ao',
			'flags-ao-overlay',
			'flags-ar',
			'flags-ar-overlay',
			'flags-as',
			'flags-as-overlay',
			'flags-at',
			'flags-at-overlay',
			'flags-au',
			'flags-au-overlay',
			'flags-aw',
			'flags-aw-overlay',
			'flags-ax',
			'flags-ax-overlay',
			'flags-az',
			'flags-az-overlay',
			'flags-ba',
			'flags-ba-overlay',
			'flags-bb',
			'flags-bb-overlay',
			'flags-bd',
			'flags-bd-overlay',
			'flags-be',
			'flags-be-overlay',
			'flags-bf',
			'flags-bf-overlay',
			'flags-bg',
			'flags-bg-overlay',
			'flags-bh',
			'flags-bh-overlay',
			'flags-bi',
			'flags-bi-overlay',
			'flags-bj',
			'flags-bj-overlay',
			'flags-bm',
			'flags-bm-overlay',
			'flags-bn',
			'flags-bn-overlay',
			'flags-bo',
			'flags-bo-overlay',
			'flags-br',
			'flags-br-overlay',
			'flags-bs',
			'flags-bs-overlay',
			'flags-bt',
			'flags-bt-overlay',
			'flags-bv',
			'flags-bv-overlay',
			'flags-bw',
			'flags-bw-overlay',
			'flags-by',
			'flags-by-overlay',
			'flags-bz',
			'flags-bz-overlay',
			'flags-ca',
			'flags-ca-overlay',
			'flags-catalonia',
			'flags-catalonia-overlay',
			'flags-cc',
			'flags-cc-overlay',
			'flags-cd',
			'flags-cd-overlay',
			'flags-cf',
			'flags-cf-overlay',
			'flags-cg',
			'flags-cg-overlay',
			'flags-ch',
			'flags-ch-overlay',
			'flags-ci',
			'flags-ci-overlay',
			'flags-ck',
			'flags-ck-overlay',
			'flags-cl',
			'flags-cl-overlay',
			'flags-cm',
			'flags-cm-overlay',
			'flags-cn',
			'flags-cn-overlay',
			'flags-co',
			'flags-co-overlay',
			'flags-cr',
			'flags-cr-overlay',
			'flags-cs',
			'flags-cs-overlay',
			'flags-cu',
			'flags-cu-overlay',
			'flags-cv',
			'flags-cv-overlay',
			'flags-cx',
			'flags-cx-overlay',
			'flags-cy',
			'flags-cy-overlay',
			'flags-cz',
			'flags-cz-overlay',
			'flags-de',
			'flags-de-overlay',
			'flags-dj',
			'flags-dj-overlay',
			'flags-dk',
			'flags-dk-overlay',
			'flags-dm',
			'flags-dm-overlay',
			'flags-do',
			'flags-do-overlay',
			'flags-dz',
			'flags-dz-overlay',
			'flags-ec',
			'flags-ec-overlay',
			'flags-ee',
			'flags-ee-overlay',
			'flags-eg',
			'flags-eg-overlay',
			'flags-eh',
			'flags-eh-overlay',
			'flags-england',
			'flags-england-overlay',
			'flags-er',
			'flags-er-overlay',
			'flags-es',
			'flags-es-overlay',
			'flags-et',
			'flags-et-overlay',
			'flags-europeanunion',
			'flags-europeanunion-overlay',
			'flags-fam',
			'flags-fam-overlay',
			'flags-fi',
			'flags-fi-overlay',
			'flags-fj',
			'flags-fj-overlay',
			'flags-fk',
			'flags-fk-overlay',
			'flags-fm',
			'flags-fm-overlay',
			'flags-fo',
			'flags-fo-overlay',
			'flags-fr',
			'flags-fr-overlay',
			'flags-ga',
			'flags-ga-overlay',
			'flags-gb',
			'flags-gb-overlay',
			'flags-gd',
			'flags-gd-overlay',
			'flags-ge',
			'flags-ge-overlay',
			'flags-gf',
			'flags-gf-overlay',
			'flags-gh',
			'flags-gh-overlay',
			'flags-gi',
			'flags-gi-overlay',
			'flags-gl',
			'flags-gl-overlay',
			'flags-gm',
			'flags-gm-overlay',
			'flags-gn',
			'flags-gn-overlay',
			'flags-gp',
			'flags-gp-overlay',
			'flags-gq',
			'flags-gq-overlay',
			'flags-gr',
			'flags-gr-overlay',
			'flags-gs',
			'flags-gs-overlay',
			'flags-gt',
			'flags-gt-overlay',
			'flags-gu',
			'flags-gu-overlay',
			'flags-gw',
			'flags-gw-overlay',
			'flags-gy',
			'flags-gy-overlay',
			'flags-hk',
			'flags-hk-overlay',
			'flags-hm',
			'flags-hm-overlay',
			'flags-hn',
			'flags-hn-overlay',
			'flags-hr',
			'flags-hr-overlay',
			'flags-ht',
			'flags-ht-overlay',
			'flags-hu',
			'flags-hu-overlay',
			'flags-id',
			'flags-id-overlay',
			'flags-ie',
			'flags-ie-overlay',
			'flags-il',
			'flags-il-overlay',
			'flags-in',
			'flags-in-overlay',
			'flags-io',
			'flags-io-overlay',
			'flags-iq',
			'flags-iq-overlay',
			'flags-ir',
			'flags-ir-overlay',
			'flags-is',
			'flags-is-overlay',
			'flags-it',
			'flags-it-overlay',
			'flags-jm',
			'flags-jm-overlay',
			'flags-jo',
			'flags-jo-overlay',
			'flags-jp',
			'flags-jp-overlay',
			'flags-ke',
			'flags-ke-overlay',
			'flags-kg',
			'flags-kg-overlay',
			'flags-kh',
			'flags-kh-overlay',
			'flags-ki',
			'flags-ki-overlay',
			'flags-km',
			'flags-km-overlay',
			'flags-kn',
			'flags-kn-overlay',
			'flags-kp',
			'flags-kp-overlay',
			'flags-kr',
			'flags-kr-overlay',
			'flags-kw',
			'flags-kw-overlay',
			'flags-ky',
			'flags-ky-overlay',
			'flags-kz',
			'flags-kz-overlay',
			'flags-la',
			'flags-la-overlay',
			'flags-lb',
			'flags-lb-overlay',
			'flags-lc',
			'flags-lc-overlay',
			'flags-li',
			'flags-li-overlay',
			'flags-lk',
			'flags-lk-overlay',
			'flags-lr',
			'flags-lr-overlay',
			'flags-ls',
			'flags-ls-overlay',
			'flags-lt',
			'flags-lt-overlay',
			'flags-lu',
			'flags-lu-overlay',
			'flags-lv',
			'flags-lv-overlay',
			'flags-ly',
			'flags-ly-overlay',
			'flags-ma',
			'flags-ma-overlay',
			'flags-mc',
			'flags-mc-overlay',
			'flags-md',
			'flags-md-overlay',
			'flags-me',
			'flags-me-overlay',
			'flags-mg',
			'flags-mg-overlay',
			'flags-mh',
			'flags-mh-overlay',
			'flags-mk',
			'flags-mk-overlay',
			'flags-ml',
			'flags-ml-overlay',
			'flags-mm',
			'flags-mm-overlay',
			'flags-mn',
			'flags-mn-overlay',
			'flags-mo',
			'flags-mo-overlay',
			'flags-mp',
			'flags-mp-overlay',
			'flags-mq',
			'flags-mq-overlay',
			'flags-mr',
			'flags-mr-overlay',
			'flags-ms',
			'flags-ms-overlay',
			'flags-mt',
			'flags-mt-overlay',
			'flags-mu',
			'flags-mu-overlay',
			'flags-mv',
			'flags-mv-overlay',
			'flags-mw',
			'flags-mw-overlay',
			'flags-mx',
			'flags-mx-overlay',
			'flags-my',
			'flags-my-overlay',
			'flags-mz',
			'flags-mz-overlay',
			'flags-na',
			'flags-na-overlay',
			'flags-nc',
			'flags-nc-overlay',
			'flags-ne',
			'flags-ne-overlay',
			'flags-nf',
			'flags-nf-overlay',
			'flags-ng',
			'flags-ng-overlay',
			'flags-ni',
			'flags-ni-overlay',
			'flags-nl',
			'flags-nl-overlay',
			'flags-no',
			'flags-no-overlay',
			'flags-np',
			'flags-np-overlay',
			'flags-nr',
			'flags-nr-overlay',
			'flags-nu',
			'flags-nu-overlay',
			'flags-nz',
			'flags-nz-overlay',
			'flags-om',
			'flags-om-overlay',
			'flags-pa',
			'flags-pa-overlay',
			'flags-pe',
			'flags-pe-overlay',
			'flags-pf',
			'flags-pf-overlay',
			'flags-pg',
			'flags-pg-overlay',
			'flags-ph',
			'flags-ph-overlay',
			'flags-pk',
			'flags-pk-overlay',
			'flags-pl',
			'flags-pl-overlay',
			'flags-pm',
			'flags-pm-overlay',
			'flags-pn',
			'flags-pn-overlay',
			'flags-pr',
			'flags-pr-overlay',
			'flags-ps',
			'flags-ps-overlay',
			'flags-pt',
			'flags-pt-overlay',
			'flags-pw',
			'flags-pw-overlay',
			'flags-py',
			'flags-py-overlay',
			'flags-qa',
			'flags-qa-overlay',
			'flags-re',
			'flags-re-overlay',
			'flags-ro',
			'flags-ro-overlay',
			'flags-rs',
			'flags-rs-overlay',
			'flags-ru',
			'flags-ru-overlay',
			'flags-rw',
			'flags-rw-overlay',
			'flags-sa',
			'flags-sa-overlay',
			'flags-sb',
			'flags-sb-overlay',
			'flags-sc',
			'flags-sc-overlay',
			'flags-scotland',
			'flags-scotland-overlay',
			'flags-sd',
			'flags-sd-overlay',
			'flags-se',
			'flags-se-overlay',
			'flags-sg',
			'flags-sg-overlay',
			'flags-sh',
			'flags-sh-overlay',
			'flags-si',
			'flags-si-overlay',
			'flags-sj',
			'flags-sj-overlay',
			'flags-sk',
			'flags-sk-overlay',
			'flags-sl',
			'flags-sl-overlay',
			'flags-sm',
			'flags-sm-overlay',
			'flags-sn',
			'flags-sn-overlay',
			'flags-so',
			'flags-so-overlay',
			'flags-sr',
			'flags-sr-overlay',
			'flags-st',
			'flags-st-overlay',
			'flags-sv',
			'flags-sv-overlay',
			'flags-sy',
			'flags-sy-overlay',
			'flags-sz',
			'flags-sz-overlay',
			'flags-tc',
			'flags-tc-overlay',
			'flags-td',
			'flags-td-overlay',
			'flags-tf',
			'flags-tf-overlay',
			'flags-tg',
			'flags-tg-overlay',
			'flags-th',
			'flags-th-overlay',
			'flags-tj',
			'flags-tj-overlay',
			'flags-tk',
			'flags-tk-overlay',
			'flags-tl',
			'flags-tl-overlay',
			'flags-tm',
			'flags-tm-overlay',
			'flags-tn',
			'flags-tn-overlay',
			'flags-to',
			'flags-to-overlay',
			'flags-tr',
			'flags-tr-overlay',
			'flags-tt',
			'flags-tt-overlay',
			'flags-tv',
			'flags-tv-overlay',
			'flags-tw',
			'flags-tw-overlay',
			'flags-tz',
			'flags-tz-overlay',
			'flags-ua',
			'flags-ua-overlay',
			'flags-ug',
			'flags-ug-overlay',
			'flags-um',
			'flags-um-overlay',
			'flags-us',
			'flags-us-overlay',
			'flags-uy',
			'flags-uy-overlay',
			'flags-uz',
			'flags-uz-overlay',
			'flags-va',
			'flags-va-overlay',
			'flags-vc',
			'flags-vc-overlay',
			'flags-ve',
			'flags-ve-overlay',
			'flags-vg',
			'flags-vg-overlay',
			'flags-vi',
			'flags-vi-overlay',
			'flags-vn',
			'flags-vn-overlay',
			'flags-vu',
			'flags-vu-overlay',
			'flags-wales',
			'flags-wales-overlay',
			'flags-wf',
			'flags-wf-overlay',
			'flags-ws',
			'flags-ws-overlay',
			'flags-ye',
			'flags-ye-overlay',
			'flags-yt',
			'flags-yt-overlay',
			'flags-za',
			'flags-za-overlay',
			'flags-zm',
			'flags-zm-overlay',
			'flags-zw',
			'flags-zw-overlay',
			'flags-multiple'
		)
	);

}

?>