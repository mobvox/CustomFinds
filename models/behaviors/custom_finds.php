<?php
/**
 * CustomFinds Behavior class
 * 
 * Behavior for CakePHP that enables you to configure custom
 * queries in your Model classes.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author     Ariel Patschiki, Daniel L. Pakuschewski
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright  Copyright 2010, MobVox Soluções Digitais.
 * @version    0.1
 */
 
 /**
  * modified:
  * - added key: remove (to remove some custom fields again)
  * - rewritten method: modifyQuery()
  * - test case added
  * 2011-07-12 ms
  */
class CustomFindsBehavior extends ModelBehavior {

	/**
	 * Prevent that Containable is loaded after CustomFinds.
	 * Containable Behavior need to be loaded before CustomFinds Behavior.
	 * @param Model $model
	 * @param array $query 
	 */
	function __verifyContainable($model, $query) {
		if (is_array($model->actsAs) && in_array('Containable', $model->actsAs) && isset($query['contain'])) {
			if (array_search('CustomFinds', $model->actsAs) > array_search('Containable', $model->actsAs)) {
				trigger_error(__('The behavior "Containable", if used together with "CustomFinds" needs to be loaded before.'), E_USER_WARNING);
			}
		}
	}
	
	function __modifyQuery(&$model, $query) {
		$customQuery = $model->customFinds[$query['custom']];
		unset($query['custom']);
		
		if (isset($query['remove'])) {
			$removes = (array)$query['remove'];
			unset($query['remove']);
			$this->__remove($customQuery, $removes);
		}
		return Set::merge($customQuery, $query);			
	}
	
	//TODO: fixme for deeper arrays
	function __remove(&$query, $removes) {
		foreach ($removes as $key => $remove) {
			//$query = Set::remove($query, $remove); # doesnt work due to dot syntax
			if (is_string($remove)) {
				if (isset($query[$remove])) {
					unset($query[$remove]);
				}
				return;
			}
			foreach ($remove as $subKey => $subRemove) {
				if (is_string($subKey) && isset($query[$remove][$subKey])) {
					return $this__remove($query[$remove][$subKey], $subRemove);
				}

				if (is_string($subRemove)) {
					if (isset($query[$key][$subRemove])) {
						unset($query[$key][$subRemove]);
						return;
					}
					/*
					if (is_string($subKey) && isset($subRemove, $query[$key][$subKey])) {
						continue;
					}
					*/
					/*
					if (!isset($query[$remove])) {
						continue;
					}
					*/
					/*
					$element = array_shift(array_keys($query[$key], $subRemove));
					unset($query[$key][$element]);
					return;
					*/
				}
				//return $this->__remove($query[$key], $subRemove);
			}
		}
	}
	
	/**
	 * Get customFinds at Model and merge with query.
	 * @param Model $model
	 * @param array $query
	 * @return array
	 */
	function beforeFind(&$model, $query) {
		if (isset($model->customFinds) && isset($query['custom']) && isset($model->customFinds[$query['custom']])) {
			$query = $this->__modifyQuery($model, $query);
			$this->__verifyContainable($model, $query);
			return $query;
		}
		return true;
	}


}