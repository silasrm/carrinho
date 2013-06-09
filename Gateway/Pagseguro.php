<?php

namespace Carrinho\Gateway;

require_once __DIR__ . '/GatewayAbstract.php';
require_once __DIR__ . '/../Exception/SemItens.php';
require_once __DIR__ . '/../Exception/ClienteInvalido.php';
require_once __DIR__ . '/../Exception/EnderecoInvalido.php';

class Pagseguro extends GatewayAbstract
{
	public function paga($urlRecirect = null)
	{
		if(count($this->getCarrinho()->getItens()) == 0)
		{
			throw new Exception\SemItens;
		}

		if(is_null($this->getCarrinho()->getCliente()))
		{
			throw new Exception\ClienteInvalido;
		}

		if(is_null($this->getCarrinho()->getEndereco()))
		{
			throw new Exception\EnderecoInvalido;
		}

		$cep = str_replace(array('-', '.'), null, $this->getCarrinho()->getEndereco()->cep);
		$address = new \PagSeguroAddress();
		$address->setPostalCode($cep);
		$address->setStreet($this->getCarrinho()->getEndereco()->rua);
		$address->setNumber($this->getCarrinho()->getEndereco()->numero);
		$address->setComplement($this->getCarrinho()->getEndereco()->complemento);
		$address->setDistrict($this->getCarrinho()->getEndereco()->bairro);
		$address->setCity($this->getCarrinho()->getEndereco()->cidade);
		$address->setState($this->getCarrinho()->getEndereco()->estado);
		$address->setCountry('BRA');

		$shippingType = new \PagSeguroShippingType();
		$shippingType->setByType('NOT_SPECIFIED');

		$paymentRequest = new \PagSeguroPaymentRequest();
		$paymentRequest->setCurrency("BRL");
		$paymentRequest->setShippingAddress($address);
		$paymentRequest->setShippingType($shippingType);

		setlocale(LC_MONETARY, 'en_US');

		$itens = $this->getCarrinho()->getItens();
		foreach($itens as $item)
		{
			$paymentRequest->addItem(
				$item->id,
				$item->nome,
				1,
				money_format('%!i', $item->valor_total),
				0,
				0
			);
		}

		$valorDescontos = 0;
		if( !is_null($this->getCarrinho()->getCupom()) )
		{
			$valorDescontos += $this->getCarrinho()->getCupom()->valor;
		}

		if( $valorDescontos > 0 )
		{
			$paymentRequest->setExtraAmount(-money_format('%!i', $valorDescontos));
		}

		$extra = $this->getExtra();
		if(count($extra) > 0 && array_key_exists('referencia', $extra))
		{
			$paymentRequest->setReference($extra['referencia']);
		}

		$_telefone = explode(' ', $this->getCarrinho()->getCliente()->telefone);

		$ddd = str_replace(array('-', '.', '(', ')'), null, trim($_telefone[0]));
		$telefone = str_replace(array('-', ' ', '.'), null, trim($_telefone[1]));

		$paymentRequest->setSender(
			substr($this->getCarrinho()->getCliente()->nome, 0, 50),
			$this->getCarrinho()->getCliente()->email,
			$ddd,
			$telefone
		);

		if(!empty($urlRecirect))
		{
			$paymentRequest->setRedirectUrl($urlRecirect);
		}

		$config = $this->getConfig();
		$credentials = new \PagSeguroAccountCredentials(
			$config['email'],
			$config['token']
		);

		$url = $paymentRequest->register($credentials);

		if($url)
		{
			$this->getCarrinho()->limpa(\Carrinho\Carrinho::LIMPA_ALL);
		}

		return $url;
	}
}