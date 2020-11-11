<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FEL Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" of FEL to make requests to the 
    | API, if is set to 'testing' will be fake requests, if is set 'production'
    | will be real requests to defined endpoints. 
    |
    */

    'env' => env('FEL_ENV', 'testing'),

    'urlToSign'             => env('FEL_URL_TO_SIGN', ''),
    'userToSign'            => env('FEL_USER_TO_SIGN', ''),
    'tokenToSign'           => env('FEL_TOKEN_TO_SIGN', ''),
    'userToCertify'         => env('FEL_USER_TO_CERTIFY', ''),
    'tokenToCertify'        => env('FEL_TOKEN_TO_CERTIFY', ''),
    'urlToCertify'          => env('FEL_URL_TO_CERTIFY', ''),
    'urlToCancel'           => env('FEL_URL_TO_CANCEL', ''),
    'codigoMoneda'          => env('FEL_CODIGO_MONEDA', ''),
    'afiliacionIVA'         => env('FEL_AFILIACION_IVA', ''),
    'codigoEstablecimiento' => env('FEL_CODIGO_ESTABLECIMIENTO', ''),
    'nitEmisor'             => env('FEL_NIT_EMISOR', ''),
    'correoEmisor'          => env('FEL_CORREO_EMISOR', ''),
    'nombreComercial'       => env('FEL_NOMBRE_COMERCIAL', ''),
    'nombreEmisor'          => env('FEL_NOMBRE_EMISOR', ''),
    'direccionEmisor'       => env('FEL_DIRECCION_EMISOR', ''),
    'codigoPostalEmisor'    => env('FEL_CODIGO_POSTAL_EMISOR', ''),
    'municipioEmisor'       => env('FEL_MUNICIPIO_EMISOR', ''),
    'departamentoEmisor'    => env('FEL_DEPARTAMENTO_EMISOR', ''),
    'paisEmisor'            => env('FEL_PAIS_EMISOR', ''),
    'codigoEscenario'       => env('FEL_CODIGO_ESCENARIO', ''),
    'tipoFrase'             => env('FEL_TIPO_FRASE', ''),

];
