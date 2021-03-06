<?php

namespace Carrinho\Storage;

require_once __DIR__ . '/StorageAbstract.php';

class Session extends StorageAbstract
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

	public function getIterator()
	{
		return new \ArrayObject($_SESSION[$this->getStorageName()]);
	}

	public function __get($name)
	{
		if($name === '')
		{
			throw new Exception('O parâmetro "' . $name . '" não pode ser vazio.');
		}

		return $_SESSION[$this->getStorageName()][$name];
	}

	public function __set($name, $value)
	{
		if ($name === '') {
			throw new Exception('O parâmetro "' . $name . '" não pode ser vazio.');
		}

		$name = (string) $name;

		$_SESSION[$this->getStorageName()][$name] = $value;
	}

	public function remove()
	{
		unset($_SESSION[$this->getStorageName()]);
	}
}