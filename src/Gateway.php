<?php

namespace Omnipay\Mpesa;

use Omnipay\Common\AbstractGateway;

/**
 * Mpesa Gateway
 *
 * This gateway is useful for testing. It simply authorizes any payment made using a valid
 * credit card number and expiry.
 *
 * Any card number which passes the Luhn algorithm and ends in an even number is authorized,
 * for example: 4242424242424242
 *
 * Any card number which passes the Luhn algorithm and ends in an odd number is declined,
 * for example: 4111111111111111
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the Mpesa Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('Mpesa');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'testMode' => true, // Doesn't really matter what you use here.
 * ));
 *
 * // Create a credit card object
 * // This card can be used for testing.
 * $card = new CreditCard(array(
 *             'firstName'    => 'Example',
 *             'lastName'     => 'Customer',
 *             'number'       => '4242424242424242',
 *             'expiryMonth'  => '01',
 *             'expiryYear'   => '2020',
 *             'cvv'          => '123',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'amount'                   => '10.00',
 *     'currency'                 => 'AUD',
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class Gateway extends AbstractGateway
{
    protected $state;
    protected $endpoint = 'https://www.enetonlinesolutions.co.ke/portal/clients/modules/addons/kenpesapb/api.php';

    public function getName()
    {
        return 'Mpesa';
    }

    public function getDefaultParameters()
    {
        return array();
    }

    public function getUrl( $data )
    {
        return $this->endpoint.'?'.http_build_query( $data );
    }

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     * @return stdClass|array
     *
     * @todo make a response and request object like every other omnipay.
     */
    public function purchase(array $data = array())
    {
        $httpRequest = $this->httpClient->createRequest( 'GET', $this->getUrl( $data ));
        $httpRequest->send();
        $response = $httpRequest->send();
        $this->state = true;
        return $response->json();
    }

    public function isSuccessful()
    {
        return $this->state;
    }
}
