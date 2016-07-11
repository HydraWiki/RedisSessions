<?php
/**
 * Curse Inc.
 * Redis Session
 * Redis Session Extension
 *
 * @author		Timothy Aldrdige
 * @copyright	(c) 2013 Curse Inc.
 * @license		GPL v3.0
 * @package		Redis Session
 * @link		https://github.com/HydraWiki/RedisSessions
 *
**/
/******************************************/
/* Credits                                */
/******************************************/
$wgExtensionCredits['other'][] = [
	'path'				=> __FILE__,
	'name'				=> 'Redis Sessions',
	'author'			=> ['Timothy Aldridge', 'Alexia E. Smith', 'Curse Inc&copy;'],
	'descriptionmsg'	=> 'redis_sessions_description',
	'version'			=> '1.2',
	'license-name'		=> 'GPL-3.0',
	'url'				=> 'https://github.com/HydraWiki/RedisSessions'
];

/******************************************/
/* Language Strings, Page Aliases, Hooks  */
/******************************************/
$extDir = __DIR__;

$wgExtensionMessagesFiles['SpecialDynamicSettings'] = "{$extDir}/RedisSessions.i18n.php";

require_once(__DIR__.'/RedisSessions.class.php');
$RedisSessionHandler = new RedisSessionHandler($wgCookieExpiration);
session_set_save_handler($RedisSessionHandler, true);

$wgMessagesDirs['RedisSessions'] = __DIR__ .'/i18n';
