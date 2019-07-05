<?php

/*
 * VIES VAT number validation interface.
 */

interface vatInterface {

    public function __construct();

    public function formatVAT();

    public function requestVAT();
}

/*
 * VIES VAT number validation
 * You can verify the validity of a VAT number issued by any Member State by providing that 
 * Member State ISO and entering the VAT number to be validated.
 * 
 * Example:
 * $vat = new vatNumber("NL123456789");
 * $vat = new vatNumber("NL123456789","NL");
 */

class vatNumber implements vatInterface {

    public $valid = false;
    public $number = "";
    public $iso = "";
    public $name = "";
    public $address = "";
    public $response = "Invalid VAT number";
    public $error = "";
    private $requestDate = "";
    private $countryForce = false;
    private $urlStatus = "http://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl";
    private $urlLive = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";
    private $url = "";

    /*
     * Call the class stand-alone to receive data about the VAT number.
     * 
     * @param string $number : Full VAT number.
     * @param char(2) $iso : ISO, example EN,FR,NL etc.
     * @param boolean $forceCountry : True if you want to force to use the $iso.
     * @param boolean $status : True if you want to check the connection.
     * 
     * @return array
     */

    public function __construct($number = "", $iso = "", $forceCountry = false, $status = false) {
        $this->number = $number;
        $this->iso = $iso;
        $this->countryForce = $forceCountry;
        $this->url = $status ? $this->urlStatus : $this->urlLive;
        $this->formatVAT();
        $this->requestVAT();
    }

    /*
     * Get the VAT formatted the right way so it can be requested at the European registration.
     */

    public function formatVAT() {
        $this->number = trim(strtoupper(str_replace([" "], [""], strip_tags($this->number))));
        $pattern = '/^(AT|BE|BG|CY|CZ|DE|DK|EE|EL|ES|FI|FR|GB|HR|HU|IE|IT|LT|LU|LV|MT|NL|PL|PT|RO|SE|SI|SK)[A-Z0-9]{6,20}$/';
        if (preg_match($pattern, $this->number)) {
            (!$this->countryForce) ? $this->iso = substr($this->number, 0, 2) : '';
            $this->number = substr($this->number, 2, strlen($this->number) - 2);
        }
    }

    /*
     * Make the SOAP request to the VIES system.
     */

    public function requestVAT() {
        try {
            $client = new SoapClient($this->url, ['exceptions' => true]);
            $result = json_decode(json_encode($client->checkVat(['countryCode' => $this->iso, 'vatNumber' => $this->number])), true);
            if (isset($result["valid"]) && $result["valid"]) {
                $this->response = "VAT number is valid";
                $this->name = trim(strip_tags($result["name"]));
                $this->address = preg_replace('/[\n\r]/', ', ', trim($result["address"]));
                $this->requestDate = date("d-m-Y", strtotime($result["requestDate"]));
            }
        } catch (Exception $requestExcep) {
            $this->error = $requestExcep->getMessage();
        }
    }

}
