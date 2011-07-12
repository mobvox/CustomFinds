<?php

App::import('Behavior', 'CustomFinds.CustomFinds');
App::import('Model', 'App');
//App::import('Vendor', 'MyCakeTestCase');

class Test extends AppModel {
	
	var $name = 'Test';
	var $useTable = false;
	var $actsAs = array('CustomFinds.CustomFinds');
	
}

class CustomFindsBehaviorTest extends CakeTestCase {
	
	function setUp() {
		$this->CustomFinds = new CustomFindsBehavior();
		
		$this->Model = new Test();

		$this->Model->customFinds = array(
			'topSellers' => array(
				'fields' => array('Product.name','Product.price'),
				'contain' => array('ProductImage.source'),
				'conditions' => array('Product.countSeller >' => 20, 'Product.is_active' => 1),
				'recursive' => 1,
				//All other find options
			)
		);
	}
	
	function tearDown() {
		
	}
	
	function testObject() {
		$this->assertTrue(is_a($this->CustomFinds, 'CustomFindsBehavior'));
	}
		
	function testModify() {
		$query = array(
			'custom' => 'topSellers',
			'recursive' => 0,
			'conditions' => array('Product.count >'=>0),
		);
		
		$res = $this->Model->Behaviors->CustomFinds->beforeFind($this->Model, $query);
		pr($res);
		$queryResult = $this->Model->customFinds['topSellers'];
		$queryResult['recursive'] = 0;
		$queryResult['conditions']['Product.count >'] = 0;
		
		$this->assertTrue(!empty($res));
		$this->assertIdentical($queryResult['recursive'], $res['recursive']);
		$this->assertIdentical($queryResult['conditions'], $res['conditions']);
	}
	
	function testModifyWithRemove() {
		$query = array(
			'custom' => 'topSellers',
			'conditions' => array('Product.count >'=>0),
			'remove' => array('conditions')
		);
		
		$res = $this->Model->Behaviors->CustomFinds->beforeFind($this->Model, $query);
		pr($res);
		$queryResult = $this->Model->customFinds['topSellers'];
		$queryResult['conditions'] = array('Product.count >'=>0);
		
		$this->assertTrue(!empty($res));
		$this->assertIdentical($queryResult['recursive'], $res['recursive']);
		$this->assertIdentical($queryResult['conditions'], $res['conditions']);
		

		$query = array(
			'custom' => 'topSellers',
			'conditions' => array('Product.count >'=>0),
			'remove' => array('conditions'=>array('Product.countSeller >'))
		);
		
		$res = $this->Model->Behaviors->CustomFinds->beforeFind($this->Model, $query);
		pr($res);
		$queryResult = $this->Model->customFinds['topSellers'];
		unset($queryResult['conditions']['Product.countSeller >']);
		$queryResult['conditions']['Product.count >'] = 0;

		$this->assertTrue(!empty($res));
		$this->assertIdentical($queryResult['conditions'], $res['conditions']);		
	}
	
}
