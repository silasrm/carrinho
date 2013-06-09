<?php

namespace Carrinho;

class Cupom extends \ArrayObject
{
	public function __construct($arr = null)
	{
		if(!is_array($arr) && !($arr instanceof \ArrayObject))
		{
			$arr = array();
		}

		if(!isset($arr['valor']))
		{
			$arr['valor'] = null;
		}

		if(!isset($arr['_raw']))
		{
			$arr['_raw'] = null;
		}

		parent::__construct($arr);
	}

	public function __get($name)
	{
		if($name === '')
		{
			throw new Exception('O parâmetro "' . $name . '" não pode ser vazio.');
		}

		return $this[$name];
	}

	public function __set($name, $value)
	{
		if ($name === '') {
			throw new Exception('O parâmetro "' . $name . '" não pode ser vazio.');
		}

		$name = (string) $name;

		$this[$name] = $value;
	}
}