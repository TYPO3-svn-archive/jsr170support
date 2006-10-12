<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_jsr170support_repo"] = Array (
	"ctrl" => $TCA["tx_jsr170support_repo"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,description,connectconfig"
	),
	"feInterface" => $TCA["tx_jsr170support_repo"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:jsr170support/locallang_db.php:tx_jsr170support_repo.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:jsr170support/locallang_db.php:tx_jsr170support_repo.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"connectconfig" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:jsr170support/locallang_db.php:tx_jsr170support_repo.connectconfig",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, title;;;;2-2-2, description;;;;3-3-3, connectconfig")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>