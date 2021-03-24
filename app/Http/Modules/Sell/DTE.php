<?php

namespace App\Http\Modules\Sell;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DTE extends Model
{
  use SoftDeletes;

  protected $table = 'dtes';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'sell_id',
    'xml',
    'signing_success',
    'signing_response',
    'certifier_success',
    'certifier_response',
    'is_cancellation',
    'uuid',
  ];

  /**
   * Get the sell that owns the DTE.
   * 
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function sell()
  {
    return $this->belongsTo(SellInvoice::class);
  }

  /**
   * Initialize the DTE params to build an XML and be able to perform both signing and certification request
   *
   * @param App\Http\Modules\Sell\Sell $sell
   * @param boolean $isCancellation
   * 
   * @return void
   */
  public function initialize(Sell $sell, bool $isCancellation)
  {
    $this->urlToSign      = config('fel.urlToSign');
    $this->userToSign     = config('fel.userToSign');
    $this->tokenToSign    = config('fel.tokenToSign');

    $this->userToCertify  = config('fel.userToCertify');
    $this->tokenToCertify = config('fel.tokenToCertify');

    $this->sell            = $sell;
    $this->is_cancellation = $isCancellation;

    $this->nitEmisor        = config('fel.nitEmisor');
    $this->fechaHoraEmision = $sell->created_at->format('Y-m-d\TH:i:s-06:00');
    
    $this->idReceptor     = $sell->client->nit;
    $this->nombreReceptor = $sell->client->name;

    if ($this->is_cancellation) {
      $this->urlToCancel                 = config('fel.urlToCancel');
      $this->numeroDocumentoAAnular      = $sell->dtes()->where('certifier_success', true)->where('is_cancellation', false)->first()->uuid;
      $this->fechaEmisionDocumentoAnular = $this->fechaHoraEmision;
      $this->fechaHoraAnulacion          = $sell->deleted_at->format('Y-m-d\TH:i:s-06:00');
      $this->motivoAnulacion             = 'Venta Anulada';
    } else {
      $this->urlToCertify          = config('fel.urlToCertify');
      $this->codigoMoneda          = config('fel.codigoMoneda');
      $this->afiliacionIVA         = config('fel.afiliacionIVA');
      $this->codigoEstablecimiento = config('fel.codigoEstablecimiento');
      $this->correoEmisor          = config('fel.correoEmisor');
      $this->nombreComercial       = config('fel.nombreComercial');
      $this->nombreEmisor          = config('fel.nombreEmisor');
      $this->direccionEmisor       = config('fel.direccionEmisor');
      $this->codigoPostalEmisor    = config('fel.codigoPostalEmisor');
      $this->municipioEmisor       = config('fel.municipioEmisor');
      $this->departamentoEmisor    = config('fel.departamentoEmisor');
      $this->paisEmisor            = config('fel.paisEmisor');
      $this->codigoEscenario       = config('fel.codigoEscenario');
      $this->tipoFrase             = config('fel.tipoFrase');
      $this->items                 = $this->extractItems($sell);
    }
  }

  /**
   * Extract items from sell details building the properties required in the DTE 
   *
   * @param App\Http\Modules\Sell\Sell $sell
   * 
   * @return \Illuminate\Support\Collection
   */
  public function extractItems(Sell $sell)
  {
    return $sell->sellDetails->map(function (SellDetail $sellDetail) {
        
      $totalPrice = round($sellDetail->quantity * $sellDetail->price, 6);

      return (object) [
        'bienOServicio'  => $sellDetail->presentation->product->is_inventoriable ? 'B' : 'S',
        'numeroLinea'    => $sellDetail->item_line + 1,
        'cantidad'       => $sellDetail->quantity,
        'unidadMedida'   => substr($sellDetail->presentation->product->uom->abbreviation, 0, 3) ?: 'PZA',
        'descripcion'    => $sellDetail->presentation->description,
        'precioUnitario' => round($sellDetail->price, 6),
        'precio'         => $totalPrice,
        'descuento'      => 0,
        'montoGravable'  => round($totalPrice / 1.12, 6),
        'montoImpuesto'  => round(0.12 * $totalPrice / 1.12, 6),
        'total'          => $totalPrice,
      ];
    });
  }

  /**
   * Build an XML to Cancellation (GTAnulacionDocumento)
   *
   * @return string
   */
  public function buildXMLCancellation()
  {
    $xml = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <dte:GTAnulacionDocumento 
      xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
      xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0"
      Version="0.1">
      <dte:SAT>
        <dte:AnulacionDTE ID="DatosCertificados">
          <dte:DatosGenerales ID="DatosAnulacion" 
            NumeroDocumentoAAnular="{$this->numeroDocumentoAAnular}"
            NITEmisor="{$this->nitEmisor}"
            IDReceptor="{$this->idReceptor}"
            FechaEmisionDocumentoAnular="{$this->fechaEmisionDocumentoAnular}"
            FechaHoraAnulacion="{$this->fechaHoraAnulacion}"
            MotivoAnulacion="{$this->motivoAnulacion}" />
        </dte:AnulacionDTE>
      </dte:SAT>
    </dte:GTAnulacionDocumento>
XML;

    return trim($xml);
  }

  /**
   * Build an XML to Certification (GTDocumento)
   *
   * @return string
   */
  public function buildXMLCertification()
  {
    $xml = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <dte:GTDocumento
      xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
      xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0" 
      Version="0.4">
      <dte:SAT ClaseDocumento="dte">
        <dte:DTE ID="DatosCertificados">
          <dte:DatosEmision ID="DatosEmision">
            <dte:DatosGenerales
              CodigoMoneda="{$this->codigoMoneda}"
              FechaHoraEmision="{$this->fechaHoraEmision}" Tipo="FACT"/>
            <dte:Emisor 
              AfiliacionIVA="{$this->afiliacionIVA}"
              CodigoEstablecimiento="{$this->codigoEstablecimiento}"
              CorreoEmisor="" 
              NITEmisor="{$this->nitEmisor}"
              NombreComercial="{$this->nombreComercial}"
              NombreEmisor="{$this->nombreEmisor}">
              <dte:DireccionEmisor>
                <dte:Direccion>{$this->direccionEmisor}</dte:Direccion>
                <dte:CodigoPostal>{$this->codigoPostalEmisor}</dte:CodigoPostal>
                <dte:Municipio>{$this->municipioEmisor}</dte:Municipio>
                <dte:Departamento>{$this->departamentoEmisor}</dte:Departamento>
                <dte:Pais>{$this->paisEmisor}</dte:Pais>
              </dte:DireccionEmisor>
            </dte:Emisor>
            <dte:Receptor 
              CorreoReceptor="" 
              IDReceptor="{$this->idReceptor}"
              NombreReceptor="{$this->nombreReceptor}" />
            <dte:Frases>
              <dte:Frase 
                CodigoEscenario="{$this->codigoEscenario}" 
                TipoFrase="{$this->tipoFrase}" />
            </dte:Frases>
            <dte:Items>
              {$this->buildXMLItems()}
            </dte:Items>
            <dte:Totales>
              <dte:TotalImpuestos>
                <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="{$this->items->pluck('montoImpuesto')->sum()}"></dte:TotalImpuesto>
              </dte:TotalImpuestos>
              <dte:GranTotal>{$this->sell->total}</dte:GranTotal>
            </dte:Totales>
          </dte:DatosEmision>
        </dte:DTE>
      </dte:SAT>
    </dte:GTDocumento>
XML;

    return trim($xml);
  }

  /**
   * Create an XML only with the items
   *
   * @return string
   */
  public function buildXMLItems()
  {
    $xmlItems = '';

    foreach ($this->items as $item) {
      $xmlItem = <<<XML
        <dte:Item 
          BienOServicio="{$item->bienOServicio}" 
          NumeroLinea="{$item->numeroLinea}">
          <dte:Cantidad>{$item->cantidad}</dte:Cantidad>
          <dte:UnidadMedida>{$item->unidadMedida}</dte:UnidadMedida>
          <dte:Descripcion>{$item->descripcion}</dte:Descripcion>
          <dte:PrecioUnitario>{$item->precioUnitario}</dte:PrecioUnitario>
          <dte:Precio>{$item->precio}</dte:Precio>
          <dte:Descuento>{$item->descuento}</dte:Descuento>
          <dte:Impuestos>
            <dte:Impuesto>
              <dte:NombreCorto>IVA</dte:NombreCorto>
              <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
              <dte:MontoGravable>{$item->montoGravable}</dte:MontoGravable>
              <dte:MontoImpuesto>{$item->montoImpuesto}</dte:MontoImpuesto>
            </dte:Impuesto>
          </dte:Impuestos>
          <dte:Total>{$item->total}</dte:Total>
        </dte:Item>
XML;
      $xmlItems .= trim($xmlItem);
    }
    
    return $xmlItems;
  }

  /**
   * Make a request to Sign the DTE
   *
   * @param int|string $id
   * 
   * @return \Illuminate\Http\Client\Response
   */
  private function sign($id)
  {
    $httpClient = config('fel.env') == 'production' ? Http::withHeaders([]) : Http::fake();

    return $httpClient->withHeaders(['Content-Type' => 'application/json'])
      ->post($this->urlToSign, [
        'alias'        => $this->userToSign,
        'llave'        => $this->tokenToSign,
        'archivo'      => base64_encode($this->xml),
        'codigo'       => $id,
        'es_anulacion' => $this->is_cancellation ? 'S' : 'N',
    ]);
  }

  /**
   * Make a request to Certify or Cancel the DTE
   *
   * @param int|string $id
   * @param string $dteSigned
   * 
   * @return \Illuminate\Http\Client\Response
   */
  private function certify($id, $dteSigned)
  {
    $httpClient = config('fel.env') == 'production' ? Http::withHeaders([]) : Http::fake();

    $url = $this->is_cancellation ? $this->urlToCancel : $this->urlToCertify;

    return $httpClient->withHeaders([
      'Content-Type'  => 'application/json',
      'usuario'       => $this->userToCertify,
      'llave'         => $this->tokenToCertify,
      'identificador' => $id,
    ])->post($url, [
      'nit_emisor'   => $this->nitEmisor,
      'correo_copia' => '',
      'xml_dte'      => $dteSigned,
    ]);
  }


  /**
   * Validate that the DTE does not exist, avoiding duplicate DTEs and if it 
   * is a Cancellation it validates that a previously Certified DTE already 
   * exists. 
   *
   * @param App\Http\Modules\Sell\Sell $sell
   * @param boolean $isCancellation
   * 
   * @throws Exception
   * 
   * @return void
   */
  public function validate(Sell $sell, bool $isCancellation=false)
  {
    if ($isCancellation) {
      $dteCertified = $sell->dtes()
        ->where('certifier_success', true)
        ->where('is_cancellation',false)
        ->first();

      if (!$dteCertified) {
        throw new Exception("Sell($sell->id) dont have a DTE to be CANCELLED");
      }  
    }

    $dte = $sell->dtes()
      ->where('certifier_success', true)
      ->where('is_cancellation', $isCancellation)
      ->first();

    if ($dte) {
      $status = $isCancellation ? Sell::OPTION_STATUS_DTE_CANCELLED : Sell::OPTION_STATUS_DTE_CERTIFIED;
      
      throw new Exception("Sell($sell->id) has a DTE ($dte->id) already created, signed, validated and $status");
    }
  }

  /**
   * Perform FEL process saving the log in each step
   *
   * @param App\Http\Modules\Sell\Sell $sell
   * @param boolean $isCancellation
   * 
   * @return App\Http\Modules\Sell\DTE
   */
  public function fel(Sell $sell, bool $isCancellation=false)
  {
    try {
      $this->validate($sell, $isCancellation);
      $this->initialize($sell, $isCancellation);
      $this->xml = $isCancellation ? $this->buildXMLCancellation() : $this->buildXMLCertification();

      $dte = self::create([
        'sell_id'         => $this->sell->id,
        'xml'             => $this->xml,
        'is_cancellation' => $this->is_cancellation,
      ]);

      $signResponse = $this->sign($dte->id);

      $dte->update([
        'signing_success'  => $signResponse['resultado'],
        'signing_response' => $signResponse->body(),
      ]);

      if ($signResponse['resultado'] != true) {
        return $dte;
      }

      $certifyResponse = $this->certify($dte->id, $signResponse['archivo']);

      $dte->update([
        'certifier_success'  => $certifyResponse['resultado'],
        'certifier_response' => $certifyResponse->body(),
        'uuid'               => $certifyResponse['uuid'],
      ]);

      return $dte;
    } catch (Exception $exception) {
      Log::error($exception);

      return $this;
    }
  }
}
