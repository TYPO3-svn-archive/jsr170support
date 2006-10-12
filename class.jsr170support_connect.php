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
 * Basisklasse für  'jsr170support' Extension.
 *
 * @author	Dimitri Ebert <dimitri.ebert@dkd.de>
 */

/**
 * class jsr170support_connect
 *
 * 
 */

class jsr170support_connect{
	var  $repository; 	// das JavaObject zur JSR170 Repository
	var  $session; 		// das JavaObject von  $repository->login(null, null);
	var  $rootNode;		// das JavaObject von  $session->getRootNode(); 

/*
 *
 * # Beispielkonfiguration für Repository
 *
 * ## wenn mit rmi
 * repository.connect=rmi
 * repository.rmi.repoUrl = //localhost:1099/crx.repository
 * repository.rmi.factory = org.apache.jackrabbit.rmi.client.ClientRepositoryFactory
 * 
 * ## wenn mit jndi
 * repository.connect=jndi
 * repository.jndi.javaNamingProviderUrl = http://www.day.com/crx
 * repository.jndi.javaNamingFactoryInitial = com.day.crx.jndi.provider.MemoryInitialContextFactory
 * 
 * ## evtl noch weitere
 * #...
 * 
 * ###
 * # Repository login
 * repository.login.workspace
 * repository.login.userId
 * repository.login.password
 * 
 * 
 * #Beispiel RepositoryObject bilden
 * 
 * $JSR170Object = t3lib_div::makeInstance('jsr170support_connect');
 * $JSR170Object -> connect($conf);
 * 
 */

/**
 * Grundfunktion für Aufbau des PHP-Objektes mit Referenzen auf Repository, Session und RootNode
 *
 *
 * @param	array	Gesammtkonfiguration, erwartet die Konfiguration für getRepository und getSession (repository.login)
 */

function connect($conf){
	$this -> repository = $this -> getRepository($conf);
	$this -> session = $this -> getSession($conf['repository.']['login.']); 
	$this -> rootNode = $this -> getRootNode();
		
}

/**
 * Ruft abhängig von repository.connect die Funnktion für Verbindungsaufbau
 *
 * @param	array Repository-Konfiguration	
 * @return	object Repository
 */
	function getRepository($conf){
		$typeaconf = $conf['repository.']['connect'];
		$repository = call_user_func(array( &$this,'getRepository_'.$typeaconf) ,$conf['repository.'][$typeaconf.'.']);
	
		return $repository;
	}


/**
 * Verbindungsaufbau mit Repository durch RMI
 *
 * @param	array	Konfiguration für RMI-Aufruf
 * @return	obejct	Repository
 */
	function getRepository_rmi($conf){
 		//$repoUrl = "//localhost:1099/crx.repository";
 		//$factory = new Java('org.apache.jackrabbit.rmi.client.ClientRepositoryFactory');
		$repoUrl = $conf['repoUrl'];

		$factory = new Java($conf['factory']);
		//$repository = new Java('javax.jcr.Repository') ;
		$repository = $factory -> getRepository($repoUrl);
		return $repository;

	}

/**
 * Verbindungsaufbau mit Repository durch JNDI (nicht implementiert)
 *
 * @param	array	Konfiguration für JNDI-Aufruf
 * @return	obejct	Repository
 */
 
	function getRepository_jndi($conf){
		//nicht implementiert
	
	}

/**
 * getSession = Login-Funktion
 * erwartet $this->repository
 *
 * @param	array Loginkonfiguration mit evtl. workspace, password, user Id	
 * @return	object	Session
 */
	function getSession($conf = array()){
	
		if(empty($conf)) $session = $this -> repository -> login(null, null);
		//else andere login Möglichkeiten abhängig von der Konfiguration. 
	
		if($conf['password'] && $conf['userId']){
		   	$password = new Java('java.lang.String',$conf['password']);
			$password = $password->toCharArray();
			$credentials = new Java('javax.jcr.SimpleCredentials',$conf['userId'], $password);
		}
	
		if($conf['workspace'] && $credentials){
			$session = $this -> repository -> login($credentials, $conf['workspace']);
		}
		else if($conf['workspace'] && !$credentials){
			$session = $this -> repository -> login(null, $conf['workspace']);
		}
		else if(!$conf['workspace'] && $credentials){
			$session = $this -> repository -> login($credentials, null);
		}
		return $session;	
	}

/**
 * Generates html table with title and several statistics
 *
 * @param	array	[spaceholder]
 * @return	object RootNode
 */
	function getRootNode($conf = array()){
		return $this -> session -> getRootNode();
	}

	
}
	
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/class.jsr170support_connect.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/class.jsr170support_connect.php"]);
}	
	
?>