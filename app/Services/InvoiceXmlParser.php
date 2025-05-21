<?php

namespace App\Services;

use SimpleXMLElement;

class InvoiceXmlParser
{
  private const TFD_NAMESPACE = 'http://www.sat.gob.mx/TimbreFiscalDigital';
  private const CFDI_NAMESPACE = 'http://www.sat.gob.mx/cfd/4';

  private SimpleXMLElement $xml;

  public function __construct(string $xmlContent)
  {
    $this->xml = simplexml_load_string($xmlContent);

    if ($this->xml === false) {
      throw new \InvalidArgumentException('Invalid XML content');
    }

    $this->xml->registerXPathNamespace('tfd', self::TFD_NAMESPACE);
    $this->xml->registerXPathNamespace('cfdi', self::CFDI_NAMESPACE);
  }

  /***************
   *** Getters ***
   ***************/

  public function getUuid(): string
  {
    return trim((string) $this->xml->xpath('//tfd:TimbreFiscalDigital/@UUID')[0]);
  }

  public function getFolio(): string
  {
    $folio = trim((string) $this->xml['Folio']);

    if (empty($folio)) {
      $uuid = $this->getUuid();
      $uuidParts = explode('-', $uuid);
      $folio = end($uuidParts);
    }

    return $folio;
  }

  public function getIssuer(): string
  {
    return trim((string) $this->xml->xpath('//cfdi:Emisor/@Rfc')[0]);
  }

  public function getReceiver(): string
  {
    return trim((string) $this->xml->xpath('//cfdi:Receptor/@Rfc')[0]);
  }

  public function getCurrency(): string
  {
    return trim((string) $this->xml['Moneda']);
  }

  public function getTotal(): float
  {
    return (float) $this->xml['Total'];
  }

  public function toArray(): array
  {
    return [
      'uuid' => $this->getUuid(),
      'folio' => $this->getFolio(),
      'issuer' => $this->getIssuer(),
      'receiver' => $this->getReceiver(),
      'currency' => $this->getCurrency(),
      'total' => $this->getTotal(),
    ];
  }
}
