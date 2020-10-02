<?php

namespace Rdrnnr87;

use Dotenv\Dotenv;
use Braintree\Test\Nonces;

class MoolahTest extends \PHPUnit\Framework\TestCase
{
    protected $config;

    protected function setUp(): void
    {
        $dotenv = Dotenv::create(dirname(__DIR__, 1));
        $dotenv->load();

        $this->config = [
            'environment' => 'sandbox',
            'merchantId' => getenv('BRAINTREE_MERCHANT_ID'),
            'publicKey' => getenv('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => getenv('BRAINTREE_PRIVATE_KEY')
        ];
    }

    /** @test */
    public function it_can_generate_a_token()
    {
        $moolah = new Moolah($this->config);
        $token = $moolah->getToken();

        $this->assertEquals(strlen($token), 2408);
    }

    /** @test */
    public function it_has_a_valid_nonce()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment engine did not initialize.  You may be on a slow connection.  Please try again.');

        $moolah = new Moolah($this->config);
        $result = $moolah->charge(10.00, '');

        $moolah1 = new Moolah($this->config);
        $result = $moolah1->charge(10.00, Nonces::$transactable);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_can_add_options()
    {
        $moolah = new Moolah($this->config);
        $moolah->withOptions(['test' => 'This is a test']);

        $this->assertArrayHasKey('test', $moolah->getOptions());
        $this->assertContains('This is a test', $moolah->getOptions());
    }

    /** @test */
    public function it_can_add_a_descriptor()
    {
        $moolah = new Moolah($this->config);
        $moolah->withDescriptor(['test' => 'This is a test']);

        $this->assertArrayHasKey('test', $moolah->getDescriptor());
        $this->assertContains('This is a test', $moolah->getDescriptor());
    }

    /** @test */
    public function it_can_add_custom_fields()
    {
        $moolah = new Moolah($this->config);
        $moolah->withCustomFields(['test' => 'This is a test']);

        $this->assertArrayHasKey('test', $moolah->getCustomFields());
        $this->assertContains('This is a test', $moolah->getCustomFields());
    }

    /** @test */
    public function it_can_add_a_credit_card()
    {
        $moolah = new Moolah($this->config);
        $moolah->withCreditCard(['test' => 'This is a test']);

        $this->assertArrayHasKey('test', $moolah->getCreditCard());
        $this->assertContains('This is a test', $moolah->getCreditCard());
    }

    /** @test */
    public function it_can_add_billing_details()
    {
        $moolah = new Moolah($this->config);
        $moolah->withBilling(['test' => 'This is a test']);

        $this->assertArrayHasKey('test', $moolah->getBilling());
        $this->assertContains('This is a test', $moolah->getBilling());
    }

    /** @test */
    public function it_can_process_a_transaction()
    {
        $moolah = new Moolah($this->config);
        $result = $moolah->charge(10.00, Nonces::$transactableVisa);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_can_query_a_result()
    {
        $moolah = new Moolah($this->config);
        $this->assertNull($moolah->getResult());

        $moolah1 = new Moolah($this->config);
        $result = $moolah1->charge(10.00, Nonces::$transactableAmEx);

        $this->assertTrue($result->success);
    }

    /** @test */
    public function it_can_throw_a_cvv_does_not_match_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The CVV provided does not match card.');

        $cardholderDetails = [
            'cardholderName' => 'Test User',
            'cvv' => '200',
            'expirationMonth' => 9,
            'expirationYear' => 2018,
            'number' => '4111111111111111',
        ];

        $moolah = new Moolah($this->config);
        $result = $moolah->withCreditCard($cardholderDetails)->charge(10.00, 'fake-valid-nonce');
    }

    //Can't get this one to perform as expected
    /*public function test_that_a_cvv_not_provided_exception_can_be_thrown()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A CVV code was not provided.');

        $cardholderDetails = [
            'cardholderName'    => 'Test User',
            'cvv'               => '',
            'expirationMonth'    => 9,
            'expirationYear'    => 2018,
            'number'            => '4111111111111111',
        ];

        $purser = new PurserTestTransaction;
        $purser->testChargeCvv(10.00, 'fake-valid-nonce', $cardholderDetails);
    }*/

    /** @test */
    public function it_can_throw_a_cvv_not_verified_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The CVV provided was not verified by the bank.');

        $cardholderDetails = [
            'cardholderName' => 'Test User',
            'cvv' => '201',
            'expirationMonth' => 9,
            'expirationYear' => 2018,
            'number' => '4111111111111111',
        ];

        $moolah = new Moolah($this->config);
        $result = $moolah->withCreditCard($cardholderDetails)->charge(10.00, 'fake-valid-nonce');
    }

    /** @test */
    public function it_can_throw_a_postal_code_does_not_match_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The postal code provided does not match the card.');

        $cardholderDetails = [
            'cardholderName' => 'Test User',
            'cvv' => '123',
            'expirationMonth' => 9,
            'expirationYear' => 2018,
            'number' => '4111111111111111',
        ];

        $billingDetails = [
            'postalCode' => '20000',
        ];

        $moolah = new Moolah($this->config);
        $result = $moolah->withCreditCard($cardholderDetails)->withBilling($billingDetails)->charge(10.00, 'fake-valid-nonce');
    }

    //Can't get this one to perform as expected
    /*public function test_that_a_postal_code_was_not_provided_exception_can_be_thrown()
    {
        $this->expectException(\XxxRacing\Classes\Purser\Exception\InvalidAvs::class);
        $this->expectExceptionMessage('A postal code was not provided.');

        $cardholderDetails = [
            'cardholderName'    => 'Test User',
            'cvv'               => '123',
            'expirationMonth'    => 9,
            'expirationYear'    => 2018,
            'number'            => '4111111111111111',
        ];

        $billingDetails = [
            'postalCode'    => '',
        ];

        $purser = new PurserTestTransaction;
        $purser->testChargeAvs(10.00, 'fake-valid-nonce', $cardholderDetails, $billingDetails);
    }*/

    /** @test */
    public function it_can_throw_a_postal_code_not_verified_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The postal code provided was not verified by the bank.');

        $cardholderDetails = [
            'cardholderName' => 'Test User',
            'cvv' => '123',
            'expirationMonth' => 9,
            'expirationYear' => 2018,
            'number' => '4111111111111111',
        ];

        $billingDetails = [
            'postalCode' => '20001',
        ];

        $moolah = new Moolah($this->config);
        $result = $moolah->withCreditCard($cardholderDetails)->withBilling($billingDetails)->charge(10.00, 'fake-valid-nonce');
    }

    /** @test */
    public function it_can_throw_an_avs_system_error_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A system error prevented verification of postal code.');

        $cardholderDetails = [
            'cardholderName' => 'Test User',
            'cvv' => '123',
            'expirationMonth' => 9,
            'expirationYear' => 2018,
            'number' => '4111111111111111',
        ];

        $billingDetails = [
            'postalCode' => '30000',
        ];

        $moolah = new Moolah($this->config);
        $result = $moolah->withCreditCard($cardholderDetails)->withBilling($billingDetails)->charge(10.00, 'fake-valid-nonce');
    }

    //Can't figure out how to test this one
    /*public function test_that_a_generic_gateway_exception_can_be_thrown()
    {
        $cardholderDetails = [
            'cardholderName'    => 'Test User',
            'cvv'               => '123',
            'expirationMonth'    => 9,
            'expirationYear'    => 2018,
            'number'            => '4000111111111511',
        ];

        $purser = new PurserTestTransaction;
        $purser->testChargeCvv(10.00, 'fake-valid-nonce', $cardholderDetails);
    }*/
}
