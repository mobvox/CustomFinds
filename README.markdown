# Custom Finds Behavior

Behavior that enables you to configure custom finds in your Model class in order to use with Model::find()

## Installation

Git clone or copy plugin into plugins/custom_finds

<pre>
<?php 
class AppModel extends Model{
	//...
	var $actsAs = array('CustomFinds.CustomFinds');
	//...
}
?>
</pre>

## Usage
Model:
<pre>
<?php 
class Product extends AppModel{
	//...
	// Only necessary if you haven't initialized the behavior in your AppModel
	var $actsAs = array('CustomFinds.CustomFinds');

	var $customFinds = array(
		'topSellers' => array(
			'fields' => array('Product.name','Product.price', ...),
			'contain' => array('ProductImage.source'),
			'conditions' => array('Product.countSeller >' => 20, 'Product.is_active' => 1),
			'recursive' => 1,
			//All other find options
		)
	);
	//...
}
?>
</pre>
Controller:
<pre>
<?php
class ProductsController extends AppController{
	//...
	var $paginate = array(
		'custom' => 'topSellers',
		//'conditions' => array(...),
		//...
	);
	function index(){
		$findAll = $this->Product->find('all', array('custom' => 'topSellers', 'conditions' => array('Product.category_id' => 2)));
		$findFirst = $this->Product->find('first', array('custom' => 'topSellers'));
		$findCount = $this->Product->find('count', array('custom' => 'topSellers'));
	}
	//...
}
?>
</pre>