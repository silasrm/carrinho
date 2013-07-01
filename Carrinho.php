<?php

namespace Carrinho;

require_once __DIR__ . '/Storage/Session.php';
require_once __DIR__ . '/Item.php';
require_once __DIR__ . '/Cupom.php';
require_once __DIR__ . '/Cliente.php';
require_once __DIR__ . '/Endereco.php';

/**
 * Carrinho de contratação dos cursos
 */
class Carrinho
{
	const LIMPA_ITENS = 'itens';
	const LIMPA_CUPOM = 'cupom';
	const LIMPA_FRETE = 'frete';
	const LIMPA_CLIENTE = 'cliente';
	const LIMPA_ENDERECO = 'endereco';
	const LIMPA_ALL = 'all';

	private $_storage = null;

	public function __construct($storage = null)
	{
		if(empty($storage) || !($storage instanceof Storage))
		{
			$storage = new Storage\Session;
		}

		$this->setStorage($storage);

		if(!($this->getStorage()->itens instanceof \ArrayObject))
		{
			$this->getStorage()->itens = new \ArrayObject;
		}

		if(is_null($this->getStorage()->cupom))
		{
			$this->getStorage()->cupom = false;
		}

		if(is_null($this->getStorage()->cliente))
		{
			$this->getStorage()->cliente = false;
		}

		if(is_null($this->getStorage()->endereco))
		{
			$this->getStorage()->endereco = false;
		}

		if(is_null($this->getStorage()->frete))
		{
			$this->getStorage()->frete = false;
		}

		if(is_null($this->getStorage()->subTotal))
		{
			$this->getStorage()->subTotal = 0;
		}

		if(is_null($this->getStorage()->total))
		{
			$this->getStorage()->total = 0;
		}
	}

	public function setStorage($storage)
	{
		$this->_storage = $storage;

		return $this;
	}

	public function getStorage()
	{
		return $this->_storage;
	}

	public function setItens($itens)
	{
		if($itens instanceof \ArrayObject)
		{
			$_itens = $itens;
		}
		else
		{
			$_itens = new \ArrayObject($itens);
		}

		$this->getStorage()->itens = $_itens;

		return $this;
	}

	public function getItens()
	{
		return new \ArrayObject($this->getStorage()->itens->getArrayCopy());
	}

	public function itemExists($itemId)
	{
		return $this->getStorage()->itens->offsetExists($itemId);
	}

	public function getItem($itemId)
	{
		return $this->getStorage()->itens->offsetGet($itemId);
	}

	protected function _addItem($item)
	{
		$item->valor_total = ($item->quantidade * $item->valor_unidade);
		$this->getStorage()->itens->offsetSet($item->id, $item);

		return $this;
	}

	protected function _removeItem($item)
	{
		$this->getStorage()->itens->offsetUnset($item->id);

		return $this;
	}

	public function addItem(Item $item)
	{
		if( !$this->itemExists($item->id) )
		{
			$this->_addItem($item);
		}
		else
		{
			$_item = $this->getItem($item->id);

			if($_item instanceof Item)
			{
				$item->quantidade += $_item->quantidade;
				$this->_addItem($item);
			}
			else
			{
				$this->_addItem($item);
			}
		}

		$this->atualizaSubTotal();
		$this->atualizaTotal();

		return $this;
	}

	public function updateItem(Item $item)
	{
		$this->_addItem($item);

		$this->atualizaSubTotal();
		$this->atualizaTotal();

		return $this;
	}

	public function removeItem(Item $item)
	{
		if( $this->itemExists($item->id) )
		{
			$this->_removeItem($item);
		}

		$this->atualizaSubTotal();
		$this->atualizaTotal();

		return $this;
	}

	public function setCupom(\Carrinho\Cupom $cupom)
	{
		$this->getStorage()->cupom = $cupom;

		$this->atualizaSubTotal();
		$this->atualizaTotal();

		return $this;
	}

	public function getCupom()
	{
		return $this->getStorage()->cupom;
	}

	public function setFrete(\Carrinho\Frete $frete)
	{
		$this->getStorage()->frete = $frete;

		$this->atualizaSubTotal();
		$this->atualizaTotal();

		return $this;
	}

	public function getFrete()
	{
		return $this->getStorage()->frete;
	}

	public function atualizaSubTotal()
	{
		$itens = $this->getItens();

		$valor = 0;
		foreach($itens as $item)
		{
			$valor += $item->valor_total;
		}

		$this->setSubTotal($valor);

		return $this;
	}

	public function atualizaTotal()
	{
		$valor = $this->getSubTotal();

		$cupom = $this->getCupom();
		if(!empty($cupom) && !empty($cupom['valor']))
		{
			$valor -= $cupom['valor'];
		}

		$frete = $this->getFrete();
		if(!empty($frete) && !empty($frete['valor']))
		{
			$valor += $frete['valor'];
		}

		$this->setTotal($valor);

		return $this;
	}

	public function setTotal($total)
	{
		$this->getStorage()->total = $total;

		return $this;
	}

	public function getTotal()
	{
		return $this->getStorage()->total;
	}

	public function setSubTotal($subTotal)
	{
		$this->getStorage()->subTotal = $subTotal;

		return $this;
	}

	public function getSubTotal()
	{
		return $this->getStorage()->subTotal;
	}

	public function getCliente()
	{
	    return $this->getStorage()->cliente;
	}

	public function setCliente($cliente)
	{
	    $this->getStorage()->cliente = $cliente;

	    return $this;
	}
	public function getEndereco()
	{
	    return $this->getStorage()->endereco;
	}

	public function setEndereco($endereco)
	{
	    $this->getStorage()->endereco = $endereco;

	    return $this;
	}

	public function limpa($tipo = self::LIMPA_ALL)
	{
		switch($tipo)
		{
			case self::LIMPA_ITENS:
				$this->getStorage()->itens = new \ArrayObject;
			break;
			case self::LIMPA_CUPOM:
				$this->getStorage()->cupom = false;
			break;
			case self::LIMPA_FRETE:
				$this->getStorage()->frete = false;
			break;
			case self::LIMPA_CLIENTE:
				$this->getStorage()->cliente = false;
			break;
			case self::LIMPA_ENDERECO:
				$this->getStorage()->endereco = false;
			break;
			default:
				$this->getStorage()->itens = new \ArrayObject;
				$this->getStorage()->cupom = false;
				$this->getStorage()->cliente = false;
				$this->getStorage()->endereco = false;
				$this->getStorage()->frete = false;
			break;
		}

		$this->atualizaSubTotal();
		$this->atualizaTotal();
	}
}