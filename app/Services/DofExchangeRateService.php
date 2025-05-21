<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DofExchangeRateService
{
  private const BASE_URL = 'https://www.banxico.org.mx/SieAPIRest/service/v1/series/SF43718/datos/oportuno';
  private string $token;

  public function __construct(string $token)
  {
    $this->token = $token;
  }

  /**
   * Get the current exchange rate from DOF. Passes the token as a header.
   * You'll should have a token in your .env file to use this service. You 
   * can get one here: https://www.banxico.org.mx/SieAPIRest/service/v1/token
   *
   * @return float|null The exchange rate or null if not found
   */
  public function getExchangeRate(): ?float
  {
    try {
      $response = Http::withHeaders([
        'Bmx-Token' => $this->token
      ])->get(self::BASE_URL);

      $data = $response->json();

      return (float) $data['bmx']['series'][0]['datos'][0]['dato'];
    } catch (\Exception $e) {
      throw new \Exception('El tipo de cambio no se pudo obtener');
    }
  }
}
