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
    protected $endpoint = 'https://omnipay.eacdirectory.com/v1/';
    protected $test_endpoint = 'https://demo2.enetonlinesolutions.co.ke/portal/clients/modules/addons/kenpesapb/api.php';

    private $success = false;

    public function getName()
    {
        return 'Mpesa';
    }

    public function getDefaultParameters()
    {
        return array();
    }

      /**
     * Create an authorize request.
     *
     * @param array $parameters
     * @return \Omnipay\Mpesa\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array()) {
        return $this->createRequest('\Omnipay\Mpesa\Message\AuthorizeRequest', $parameters);
    }

    public function getUrl($data = array()) {
        return $this->endpoint;
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
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        curl_close($ch);

        
        $_data = json_decode($response);

        if (isset($_data->success)) {
            $this->success = $_data->success;
        }




        return $_data;
    }

    public function isSuccessful() {
        return $this->success;
    }
}
