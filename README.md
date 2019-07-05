# VIES Europe VAT number validation
You can verify the validity of a VAT number issued by any Member State by providing that Member State ISO and entering the VAT number to be validated.

## Usage
#### When you have the full VAT number:
```sh
$vat = new vatNumber("HERE_VAT_NUMBER");
```

#### When the first part is missing, use the second parameter to complete it:
```sh
$vat = new vatNumber("HERE_VAT_NUMBER","HERE_COUNTRY_ISO");
```
Country ISO for example: DE, FR, NL, IT etc.

#### The third parameter can be used to always use the second parameter as ISO:
```sh
$vat = new vatNumber("HERE_VAT_NUMBER","HERE_COUNTRY_ISO",true);
```

#### Parameters:
```sh
@param string $number : Full VAT number.
@param char(2) $iso : ISO, example EN,FR,NL etc.
@param boolean $forceCountry : True if you want to force to use the $iso.
@param boolean $status : True if you want to check the connection.

@return array
```
