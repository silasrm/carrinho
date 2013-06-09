<?php

namespace Carrinho\Gateway;

abstract class GatewayAbstract
{
	protected $_carrinho = null;
	protected $_config = null;

	public function __construct(Carrinho $carrinho = null, array $config = null, array $extra = null)
	{
		if(!empty($carrinho))
		{
			$this->setCarrinho($carrinho);
		}

		if(!empty($config))
		{
			$this->setConfig($config);
		}

		if(!empty($extra))
		{
			$this->setExtra($extra);
		}
	}

	public function getCarrinho()
	{
		return $this->_carrinho;
	}

	public function setCarrinho($carrinho)
	{
		$this->_carrinho = $carrinho;

		return $this;
	}

	public function getConfig()
	{
		return $this->_config;
	}

	public function setConfig($config)
	{
		$this->_config = $config;

		return $this;
	}

	public function getExtra()
	{
		return $this->_extra;
	}

	public function setExtra($extra)
	{
		$this->_extra = $extra;

		return $this;
	}

	public abstract function paga($urlRecirect = null);
}