<?php

class CustomFindsBehavior extends ModelBehavior {
	function __verificaContainable($model, $query) {
		if (is_array($model->actsAs) && in_array('Containable', $model->actsAs) && isset($query['contain'])) {
			if (array_search('CustomFinds', $model->actsAs) > array_search('Containable', $model->actsAs)) {
				trigger_error(__('The behavior "Containable", is used together with "CustomFinds" needs to be loaded before.'), E_USER_WARNING);
			}
		}
	}	
	function beforeFind(&$model, $query) {
		if (isset($model->customFinds) && isset($query['custom']) && isset($model->customFinds[$query['custom']])) {
			$query = Set::merge($model->customFinds[$query['custom']], $query);
			$this->__verificaContainable($model, $query);
			unset($query['custom']);
			return $query;
		}
		return true;
	} 
}