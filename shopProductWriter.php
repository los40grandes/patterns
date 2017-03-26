<?php
//shopProduct before the variable is a Class Type Hint which allows specific instance of an object to be provided to the method.  Nice ah?! :)
abstract class shopProductWriter{
	protected $products = array();

	public function AddProduct(shopProduct $shopProduct)
	{
		$this->products[] = $shopProduct;
	}
	abstract public function write();
}

class XmlProductWriter extends shopProductWriter{
	public function write(){
			$str = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$str .= "<products>\n";
			foreach ( $this->products as $shopProduct ) {
				$str .= "\t<product title=\"{$shopProduct->getTitle()}\">\n";
				$str .= "\t\t<summary>\n";
				$str .= "\t\t{$shopProduct->getSummaryLine()}\n";
				$str .= "\t\t</summary>\n";
				$str .= "\t</product>\n";
			}
			$str .= "</products>\n";
			return $str;
	}
}
class TextProductWriter extends shopProductWriter{
	public function write() {
		$str = "PRODUCTS:".PHP_EOL."<br>";
		foreach ( $this->products as $shopProduct ) {
			$str .= $shopProduct->getSummaryLine().PHP_EOL."<br>";
		}
		echo $str;
	}
}
