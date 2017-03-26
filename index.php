<?php
include "shopProductWriter.php";
include "Chargeable-interface.php";
include "DomainObject.php";
class shopProduct implements Chargeable{
	private $title;
	private $producerMainName  = "main name";
	private $producerFirstName = "first name";
	protected $price           = 0;
	private $id                = 0;

	const AVAILABLE    = 1;
	const OUT_OF_STOCK = 0;
	public function __construct($title, $firstName, $mainName, $price){
		$this->title             = $title;
		$this->producerMainName  = $mainName;
		$this->producerFirstName = $firstName;
		$this->price             = $price;
	}

	public function setID($id)
	{
		$this->id = $id;
	}

	public static function getInstance($id, PDO $pdo)
	{
		$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");

		$result = $stmt->execute(array($id));

		$row = $stmt->fetch();

		if(empty($row)) {return null;}

		if($row['type'] == 'book'){
			$product = new BookProduct (
				$row['title'],
				$row['firstname'],
				$row['mainname'],
				$row['price'],
				$row['numpages']);
		}
		else if ($row['type'] == 'cd'){
			$product= new  CdProduct(
				$row['title'],
				$row['firstName'],
				$row['mainname'],
				$row['price'],
				$row['playlength']);
		}
		else {
			$product = new shopProduct(
				$row['title'],
				$row['firstName'],
				$row['mainname'],
				$row['price']);
		}
		$product->setID($row['id']);
		return $product;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getMainName()
	{
		return $this->producerMainName;
	}

	public function getFirstName()
	{
		return $this->producerFirstName;
	}

	public function getProducer() {
		return "{$this->producerFirstName} "."{$this->producerMainName}";
	}

	public function getPrice(){
		return $this->price;
	}

	public function getSummaryLine(){
		$base = "$this->title ( {$this->producerMainName}, ";
		$base.= "{$this->producerFirstName} )";
		return $base;
	}
}

/**
 * CD Product
 */
class CdProduct extends shopProduct {
	private $playLength = 0;

	public function __construct($title, $producerMainName, $producerFirstName, $price, $playLength)
	{
		parent::__construct($title, $producerMainName, $producerFirstName, $price);
		$this->playLength=$playLength;
	}

	public function getPlayLength()
	{
		return $this->playLength;
	}

	public function getSummaryLine()
	{
		$base = parent::getSummaryLine();
		$base.= ": playing time - {$this->playLength}";
		return $base;
	}
}

/**
 * Book Product
 */
class BookProduct extends shopProduct
{
	private $numPages = 0;

	public function __construct($title, $producerMainName, $producerFirstName, $price, $numPages)
	{
		parent::__construct($title, $producerMainName, $producerFirstName, $price);
		$this->numPages=$numPages;
	}

    public function getNumberOfPages()
    {
        return $this->numPages;
    }

    public function getSummaryLine()
    {
    	$base = parent::getSummaryLine();
		$base.= " : page count - {$this->numPages}";
		return $base;
    }
}
 /*
 *
 *	class initialization.
 */
/*$product_1 = new shopProduct("PHP objects, patterns and practices", "Cather", "Willa", 5.99);
$product_2 = new shopProduct();
$product_2->title = "PHP Cookbook";
var_dump($product_1);
var_dump($product_2);
echo $product_1->title.'<br>';
echo $product_2->title.'<br>';
///////// ----- Up to here Class instances + printing properties from it. ------//////
echo "author: {$product_1->getProducer()}\n";
testing CLASS TYPE HINTS -- real nice :) --
$writer = new shopProductWriter();
$writer->write($product_1);

$test = new Test();
$writer->write($test);
class Test{

}*/

//inheritance without child constructors.
// $product1=new BookProduct("PHP objects, patterns and practices", "Cather", "Willa", 5.99, 527, null);
// echo $product1->getSummaryLine();

//adding constructor to the child classes requires to callback to the parent constructor.

$product1=new CdProduct("PHP objects, patterns and practices", "Cather", "Willa", 5.99, 527);
$product2=new BookProduct("PHP Cookbook", "Daniel", "Yonkov", 39.99, 312);
echo $product1->getSummaryLine().'<br>';
// $writer = new shopProductWriter();
// $writer->AddProduct($product1);
// $writer->AddProduct($product2);
// $writer->write();

/// ----- Chapter IV -------- ////

//connection to the database using PDO (PHP Data Object)
//steps: 1. private ID; 2. SetID() method, 3. GetInstance() static method {Factory}

$dsn = "mysql:host=localhost;dbname=OOP";
$pdo = new PDO($dsn, 'root', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sp=shopProduct::getInstance(1, $pdo);
echo "<pre>".print_r($sp,1)."</pre>";

//class constants

echo shopProduct::AVAILABLE."<br>";

//ABSTRACT CLASS

$writer = new TextProductWriter();
$writer->AddProduct($product1);
$writer->AddProduct($product2);
$writer->write();

$writer2=new XmlProductWriter();
$writer2->AddProduct($product1);
$writer2->AddProduct($product2);
$xml=simplexml_load_string($writer2->write());
echo "<pre>".print_r($xml,1)."</pre>";
echo "<br><br><hr>";
echo "<pre>".print_r(User::create(),1)."</pre>";
echo "<pre>".print_r(Spreadsheet::create(),1)."</pre>";