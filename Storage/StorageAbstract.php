<?php

namespace Carrinho\Storage;

abstract class StorageAbstract implements \IteratorAggregate
{
	private $_storageName = '__Carrinho';

	public function __construct($storageName = null)
	{
		if(!empty($storageName))
		{
			$this->setStorageName($storageName);
		}

		if(!isset($_SESSION[$this->getStorageName()]))
		{
			$_SESSION[$this->getStorageName()] = array();
		}
	}

	public function getStorageName()
	{
		return $this->_storageName;
	}

	public function setStorageName($storageName)
	{
		$this->_storageName = $storageName;

		return $this;
	}

	public abstract function remove();

	public abstract function getIterator();

	public abstract function __get($name);

	public abstract function __set($name, $value);
}