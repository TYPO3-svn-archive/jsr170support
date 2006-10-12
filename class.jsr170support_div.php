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


//require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * class jsr170support_div
 * stellt die Funktionen für JSR170 Unterstützung
 * Informationsfunktionen, Konvertierungsfunktionen, Debugfunktionen
 */

class jsr170support_div{

// Informationsfunktionen

// Konvertierungsfunktionen

/**
 * Umwandlung von Properties eines Knoten in ein associatives Array
 *
 * @param	object	JSR170-Node
 * @return	array	Properties(nur name => value) in einem assotiativen Array, auch multiple Properties
 */

function getSimpleNodeProperties($node){
	$properties = $node->getProperties();
	$size = $properties -> getSize();
	$propertyA = array();
		
	for($i = 0; $i < $size; $i++){
		$property = $properties -> nextProperty();
		$propertydefinition = $property -> getDefinition(); 
		
		$name = $propertydefinition -> getName();
		$name= java_values($name);
		if($propertydefinition -> isMultiple() != 1) $propertyA[$name] = $property -> getString();
		else ;//$propertyA[$name] =  $this->getMultiplePropertyValues($property);
		
	}	
	return $propertyA;
}


/**
 * Umwandlung von multiplen Werten in ein Array
 *
 * @param	object	Property mit mehreren Werten
 * @return	array	Array mit Werten
 */
function getMultiplePropertyValues($property){
	$values = $property -> getValues();
 	foreach ($values as $value)
 	{
  		$rvalues[] = $value -> getString();	
 	}
 	return $rvalues;
}

/**
 * Umwandlung von Unterknoten eines Knoten in Array mit Namen von Knoten
 *
 * @param	object	Node
 * @return	array	Array mit Namen von  Unterknoten
 */
 
function getNodesArr($node){
	$nodes  = $node -> getNodes();	
	$size = $nodes -> getSize();
	for($i=0; $i < $size; $i++){
		$curnode = $nodes -> nextNode();
     	$nodesA[] = $curnode -> getName();
 	}
 	return $nodesA;	
}

// Debugfunktionen


/**
 * Node-Debug mit Properties ohne Unterknoten
 * mit direkter Ausgabe
 * @param	object	Node
 * @return	array	assotiatives Array mit allen Eigenschaften einer Property
 */
 
function debugNodeProperties($node){
	$properties = $node->getProperties();
	$size = $properties -> getSize();
	
	for($i = 0; $i < $size; $i++){
		$property = $properties -> nextProperty();
		$propertydefinition = $property -> getDefinition(); 
		$propertyA = array();
		
		$propertyA['Name'] = $propertydefinition -> getName();
		$propertyA['isMultiple'] = $propertydefinition -> isMultiple(); 
		$propertyA['ValueConstraints'] = $propertydefinition -> getValueConstraints(); 
		$propertyA['RequiredType'] = $propertydefinition -> getRequiredType(); 
		if($propertyA['isMultiple'] != 1) $propertyA['Value'] = $property -> getString();
		else $propertyA['Value'] =  $this -> getMultiplePropertyValues($property);
	}	

}


/**
 * Rekursive Debugfunktion für Knoten mit Unterknoten evtl. inklusiv Properties
 *
 * @param	object	Konfiguration für RMI-Aufruf
 * @param	integer	rekursiv oder nicht rekursiv
 * @param	integer	mit oder ohne Properties
 * @param	integer	für Ausgabekontrolle
 * @return	array	array mit Knoten, Unterknoten und Properties
 */

function debugNodeNodes($node,$recursive = 0, $withPoroperties = 0, $level = 1 ){
	$nodes  = $node -> getNodes();	
	$size = $nodes -> getSize();

 	for($i=0; $i < $size; $i++){
		$curnode = $nodes -> nextNode();
		if ($recursive){
			$nodesA[$curnode -> getName()] = $this -> debugNodeNodes($curnode,$recursive - 1, $withPoroperties, $level - 1);
			if($withPoroperties == 1) $nodesA[$curnode -> getName()]['PROPERTIES'] = $this -> getSimpleNodeProperties($curnode);	
		}
		else {
    		$nodesA[$curnode -> getName()] = '';
        	if($withPoroperties == 1) $nodesA[$curnode -> getName()]['PROPERTIES'] = $this ->  getSimpleNodeProperties($curnode);
		}
 	}
 
	if ($level == 1){ t3lib_div::debug($nodesA); }
 	else return $nodesA;
}	





	
}
	
	
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/class.jsr170support_div.php"])	{
include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/jsr170support/class.jsr170support_div.php"]);

}	
?>