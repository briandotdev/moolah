<?php

namespace Rdrnnr87;

use Exception;
use Braintree\ClientToken;
use Braintree\Transaction;
use Braintree\Configuration;

class Moolah
{
    protected $environment;
    protected $merchantId;
    protected $publicKey;
    protected $privateKey;
    protected $result;
    protected $options;
    protected $descriptor;
    protected $customFields;
    protected $creditCard;
    protected $billing;

    public function __construct($config)
    {
        $this->environment = $config['environment'];
        $this->merchantId = $config['merchantId'];
        $this->publicKey = $config['publicKey'];
        $this->privateKey = $config['privateKey'];
        $this->result = null;
        $this->options = [
            'submitForSettlement' => true,
        ];
        $this->descriptor = null;
        $this->customFields = null;
        $this->creditCard = null;
        $this->billing = null;
    }

    public function getToken()
    {
        $this->init();

        return ClientToken::generate();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getDescriptor()
    {
        return $this->descriptor;
    }

    public function getCustomFields()
    {
        return $this->customFields;
    }

    public function getCreditCard()
    {
        return $this->creditCard;
    }

    public function getBilling()
    {
        return $this->billing;
    }

    public function withOptions($options)
    {
        foreach ($options as $key => $option) {
            $this->options[$key] = $option;
        }

        return $this;
    }

    public function withDescriptor($options)
    {
        foreach ($options as $key => $option) {
            $this->descriptor[$key] = $option;
        }

        return $this;
    }

    public function withCustomFields($options)
    {
        foreach ($options as $key => $option) {
            $this->customFields[$key] = $option;
        }

        return $this;
    }

    public function withCreditCard($options)
    {
        foreach ($options as $key => $option) {
            $this->creditCard[$key] = $option;
        }

        return $this;
    }

    public function withBilling($options)
    {
        foreach ($options as $key => $option) {
            $this->billing[$key] = $option;
        }

        return $this;
    }

    public function charge($amount, $nonce)
    {
        $this->init();

        //Make sure request has supplied a valid nonce
        $this->requestHasValidNonce($nonce);

        $payload = [
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
        ];

        if (!is_null($this->descriptor)) {
            $payload['descriptor'] = $this->descriptor;
        }

        if (!is_null($this->options)) {
            $payload['options'] = $this->options;
        }

        if (!is_null($this->customFields)) {
            $payload['customFields'] = $this->customFields;
        }

        if (!is_null($this->creditCard)) {
            $payload['creditCard'] = $this->creditCard;
        }

        if (!is_null($this->billing)) {
            $payload['billing'] = $this->billing;
        }

        //Attempt the charge
        $this->result = Transaction::sale($payload);

        //Deal with errors
        if ($this->result->success === false) {
            $this->handleErrors();
        }

        return $this->getResult();
    }

    protected function init()
    {
        Configuration::environment($this->environment);
        Configuration::merchantId($this->merchantId);
        Configuration::publicKey($this->publicKey);
        Configuration::privateKey($this->privateKey);

        $this->result = null;
    }

    protected function requestHasValidNonce($nonce)
    {
        //If nonce exists in input then Braintree JS was loaded
        if ($nonce == '') {
            throw new Exception('Payment engine did not initialize.  You may be on a slow connection.  Please try again.');
        }

        return true;
    }

    protected function handleErrors()
    {
        if ($this->result->transaction->status === 'gateway_rejected') {
            if ($this->result->transaction->gatewayRejectionReason === 'cvv') {
                if ($this->result->transaction->cvvResponseCode === 'N') {
                    throw new Exception('The CVV provided does not match card.');
                }

                if ($this->result->transaction->cvvResponseCode === 'I') {
                    throw new Exception('The CVV provided does not match card.');
                }

                if ($this->result->transaction->cvvResponseCode === 'U') {
                    throw new Exception('The CVV provided was not verified by the bank.');
                }
            }

            if ($this->result->transaction->gatewayRejectionReason === 'avs') {
                if ($this->result->transaction->avsPostalCodeResponseCode === 'N') {
                    throw new Exception('The postal code provided does not match the card.');
                }

                if ($this->result->transaction->avsPostalCodeResponseCode === 'I') {
                    throw new Exception('A postal code was not provided.');
                }

                if ($this->result->transaction->avsPostalCodeResponseCode === 'U') {
                    throw new Exception('The postal code provided was not verified by the bank.');
                }

                if ($this->result->transaction->avsErrorResponseCode === 'E') {
                    throw new Exception('A system error prevented verification of postal code.');
                }
            }

            throw new Exception('An error prevented card processing.');
        }

        throw new Exception('An error prevented card processing.');
    }
}
