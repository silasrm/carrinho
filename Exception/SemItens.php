<?php

namespace Carrinho\Exception;

class SemItens extends \Exception
{
	protected $message = 'Carrinho vazio';
}