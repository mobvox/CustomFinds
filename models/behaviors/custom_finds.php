<?php

/**
 * CustomFinds Behavior class
 * 
 * Behavior for CakePHP that enables you to config custom
 * querys at Models.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author     Ariel Patschiki, Daniel L. Pakuschewski
 * @licence    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright  Copyright 2010, MobVox Soluções Digitais.
 * @version    0.1
 */
class CustomFindsBehavior extends ModelBehavior {

	/**
	 * Verify if Containable is loaded after CustomFinds.
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

	/**
	 * Get customFinds at Model and merge with query.
	 * @param Model $model
	 * @param array $query
	 * @return array
	 */
	function beforeFind(&$model, $query) {
		if (isset($model->customFinds) && isset($query['custom']) && isset($model->customFinds[$query['custom']])) {
			$query = Set::merge($model->customFinds[$query['custom']], $query);
			$this->__verifyContainable($model, $query);
			unset($query['custom']);
			return $query;
		}
		return true;
	}

}