<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Dimitri Ebert (dimitri.ebert@dkd.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * Module 'JSR170Support' for the 'jsr170support' extension.
 *
 * @author	Dimitri Ebert <dimitri.ebert@dkd.de>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);	
require ("conf.php");
require ("config.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:jsr170support/mod1/locallang.php");
#include ("locallang.php");
require_once (PATH_t3lib."class.t3lib_scbase.php");

require_once (PATH_t3lib."class.t3lib_extmgm.php");
require_once(t3lib_extMgm::extPath("jsr170support")."class.jsr170support_connect.php");
require_once(t3lib_extMgm::extPath("jsr170support")."class.jsr170support_div.php");
require_once (PATH_t3lib."class.t3lib_tcemain.php");

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_jsr170support_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $repositoryPath;
	var $repoConfId;
	/**
	 * 
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();

	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
			)
		);
		parent::menuConfig();
	}

		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		
		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{
	
				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);

			
			// Render content:
			$this->moduleContent();

			
			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}
		
			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero
		
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
		
			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}
	
	/**
	 * Generates the module content
	 */
	function moduleContent()	{
		switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
									
				$this->content.=$this->doc->section("Repository",$this -> repoNavigation() ,0,1);
			break;
		} 
	}
	

//Benutzerdefinierte Funktionen
	
function repoNavigation(){
	$path = $GLOBALS["HTTP_GET_VARS"]['repoPath'];
	$this->repoConfId = $GLOBALS["HTTP_GET_VARS"]['repoConfId'];


	if ($this->repoConfId > 0){
		// Auswahlmenu nicht mehr anzeigen
		// $conf array auslesen / konfiguration für Verbindungsaufbau
		$query = "SELECT  * FROM  tx_jsr170support_repo WHERE hidden=0 AND deleted=0 AND uid='".$this->repoConfId."'";
		$res = mysql(TYPO3_db,$query);
		$row = mysql_fetch_assoc($res);
	
		//Konfiguration konvertieren	
		$this->TSdataArray = t3lib_TSparser::checkIncludeLines_array(explode("\n",$row['connectconfig']));
		$RepoTS = implode(chr(10).'[GLOBAL]'.chr(10),$this->TSdataArray);
		$parseObj = t3lib_div::makeInstance('t3lib_TSparser');
		$parseObj->parse($RepoTS);
		$conf = $parseObj->setup;

	}
	else {
		$options='';	
    	$query = "SELECT  * FROM  tx_jsr170support_repo WHERE hidden=0 AND deleted=0";

		$res = mysql(TYPO3_db,$query);
		while($res && $row = mysql_fetch_assoc($res)){
			$getParams['repoConfId']= $row['uid'];
	 		$options.='<a href="'.t3lib_div::linkThisScript($getParams).'">'.$row['title'].'</a><br>';
		}
  		return $options.'<br><br>Repository-Configuration auswählen (falls angelegt).';	
	}

	//Initialisierung und Verbindungsaufbau 	
	$this -> JSR170Helper = t3lib_div::makeInstance('jsr170support_div');		
	$this -> JSR170Object = t3lib_div::makeInstance('jsr170support_connect');
	$this -> JSR170Object -> connect($conf);			

	//Nodes von der ersten Ebene auslesen
	$repoNodeArr = $this -> JSR170Helper -> getNodesArr($this -> JSR170Object -> rootNode);
 	
 	//Aufruf der rekursiver Funktion für Baumdarstellung
	$content1 = $this -> getRepoTable($repoNodeArr,explode('/',$path));

	//Falls ein Pfad ausgewählt Eigenschaften ausgeben
	if($this -> repositoryPath)$content2 = $this -> getNodeTable($this -> repositoryPath);
	else $content2 = '';

	//Teile zur Ausgabe zusammenfügen und weiterleiten
	$content='<table celpadding=0 cellspacing=0 style="border: 1px solid #999999;"><tr><td style="background-color: #cccccc;" valign="top">'.$content1.'</td><td valign="top">'.$content2.'</td></tr></table>';
	$content = 'Path:<b>'.$this -> repositoryPath.'</b><br>'.$content; 	
  	return $content;	
}

function getRepoTable($nodeArr,$path,$level=0){
	//Rekursive Funktion für Baumdarstellung	
	if(empty($nodeArr)) $nodeArr = array();	
	$rootNode = $this -> JSR170Object -> rootNode;
	
	foreach($nodeArr as $node){
		$node = java_values($node);
		$nodePath = '';
		for ($i=0; $i <= $level; $i++) $nodePath .= ( $i==0 ? '' : "/").$path[$i] ;	
 		$nodePath.='/'.$node;
		
		if($node == $path[$level+1]) 
		{ 
			for ($i=0; $i <= $level; $i++){ 
		  		$curPath.= ( $i==0 ? '' : "/").$path[$i+1] ;
			}
			
			$curNode = $rootNode -> getNode($curPath);
			$nodeicon = $this->getNodeIcon($curNode);
			$curNodeArr = $this -> JSR170Helper -> getNodesArr($curNode);
			$this -> repositoryPath = str_replace(' ','&nbsp;',$curPath);
		  	$nextlevel = $this -> getRepoTable($curNodeArr,$path,$level+1);
			$diricon = $this->getDirIcon(1);
		  	$nodestyle='<span style="color: #ff0000;"> | </span>';
		}
		else {
			$nodestyle='|';
		    $curNode = $rootNode -> getNode(substr($nodePath,1));
			$nextlevel = '';
			$diricon = $this->getDirIcon(0);
			$nodeicon = $this->getNodeIcon($curNode);
		}
		
		$getParams['function']=$this->MOD_SETTINGS["function"];
		$getParams['repoPath']= $nodePath;
		$getParams['repoConfId']= $this->repoConfId;
		$icons = $diricon.$nodeicon;
		$nodelink = '<a href="'.t3lib_div::linkThisScript($getParams).'" >'.str_replace('|',$node,$nodestyle).'</a>';
		$content.= '<tr><td nowrap>'.$icons.$nodelink.$nextlevel.'</td></tr>';
	}
	
	$content = '<table style="margin-left: 16px;" border=0>'.$content.'</table>';
	return $content;
	
}

function getNodeTable($path){
//Funktion für Darstellung von Properties	
	$path=str_replace('&nbsp;',' ',$path);
	$node = $this ->JSR170Object -> rootNode  -> getNode($path);
	$propArr = $this->JSR170Helper->getSimpleNodeProperties($node);
	$content = $this -> outputProperies($propArr);
	$this->JSR170Helper->debugNodeProperties($node);
	return $content;
}


function outputProperies($propArr){
//Rekursive Funktion für Darstellung einzelner Properties
	foreach ($propArr as $propname => $propvalue ){
		if(is_array($propvalue)){
			$content .= '<tr><td valign="top" style="background-color: #ffcccc; border-bottom: 1px solid #ff6666;">'.$propname.'</td><td style="background-color: #eeeeee; border-bottom: 1px solid #666666;">'.$this->outputProperies($propvalue).'</td><tr>';	
		}
		else{
			$content .= '<tr><td valign="top" style="background-color: #ffcccc; border-bottom: 1px solid #ff6666;">'.$propname.'</td><td style="background-color: #eeeeee; border-bottom: 1px solid #666666;">'.htmlspecialchars($propvalue).'</td></tr>'; 
		}
 	}	
    $content = '<table cellpadding=2 cellspacing=0 style="border: 1px solid #ff6666;">'.$content.'</table>';
	return $content;
}

function getDirIcon($pm){
	//+/- aufgeklappt oder nicht
	return $pm? '<img src="images/minus.gif" />' : '<img src="images/plus.gif" />' ;
}

function getNodeIcon(&$curNode){
	//Icon nach Type von Knoten bestimmen
	global $JSR170SUP_conf;
	$typeproperty = $curNode -> getProperty('jcr:primaryType'); 
	$typevalue = $typeproperty -> getString();
	$typevalue =  java_values($typevalue);
	$icon = $JSR170SUP_conf['icons'][$typevalue]?$JSR170SUP_conf['icons'][$typevalue]:$JSR170SUP_conf['icons']['default'];
	 return '<img src="'.$icon.'">';
}

	
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/mod1/index.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/mod1/index.php"]);
}




// Make instance:
$SOBE = t3lib_div::makeInstance("tx_jsr170support_module1");
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>