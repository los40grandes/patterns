<?php

abstract class DomainObject
{
	private $group;

	public function __construct(){
		$this->group = static::getGroup();
	}

	public static function create(){
		return new static();
	}

	static function getGroup(){
		return 'Default';
	}

}

class User extends DomainObject 
{

}

class Document extends DomainObject
{
	static function getGroup(){
		return 'Document';
	}
}

class Spreadsheet extends Document
{

}