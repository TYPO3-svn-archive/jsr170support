<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")	{
		
	t3lib_extMgm::addModule("web","txjsr170supportM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
//	t3lib_extMgm::addModule("txjsr170supportM1","","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}


t3lib_extMgm::allowTableOnStandardPages("tx_jsr170support_repo");

$TCA["tx_jsr170support_repo"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:jsr170support/locallang_db.php:tx_jsr170support_repo",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_jsr170support_repo.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, title, description, connectconfig",
	)
);
?>