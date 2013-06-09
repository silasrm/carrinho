<?php

namespace Carrinho;

class Endereco extends \ArrayObject
{
	public function __construct($arr = null)
	{
		if(!is_array($arr) && !($arr instanceof \ArrayObject))
		{
			$arr = array();
		}

		if(!isset($arr['cep']))
		{
			$arr['cep'] = null;
		}

		if(!isset($arr['rua']))
		{
			$arr['rua'] = null;
		}

		if(!isset($arr['numero']))
		{
			$arr['numero'] = null;
		}

		if(!isset($arr['complemento']))
		{
			$arr['complemento'] = null;
		}

		if(!isset($arr['bairro']))
		{
			$arr['bairro'] = null;
		}

		if(!isset($arr['cidade']))
		{
			$arr['cidade'] = null;
		}

		if(!isset($arr['estado']))
		{
			$arr['estado'] = null;
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