<?php
namespace Vsb\Gateway\Ariuspay;
class Ariuspay {
    public static $gates = [
        "test" => [// testdata
            "CaptureRequest" => [
                "url" => "https://sandbox.ariuspay.ru/paynet/api/v2/",
                "endpoint" => "1144",
                "merchant_key" => "99347351-273F-4D88-84B4-89793AE62D94",
                "merchant_login" => "GARAN24"
            ],
            "PreauthRequest" => [
                "url" => "https://sandbox.ariuspay.ru/paynet/api/v2/",
                "endpoint" => "1144",
                "merchant_key" => "99347351-273F-4D88-84B4-89793AE62D94",
                "merchant_login" => "GARAN24"
            ]
        ],
        "akbars" =>[
            "SaleRequest" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "2879",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ],
            "CaptureRequest" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "2879",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ],
            "PreauthRequest" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "3028",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ],
            "CreateCardRef_RIB" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "3028",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ],
            "CreateCardRef" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "2879",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ]
        ],
        "lemonway" =>[
            "CaptureRequest" => [
                "url" => "https://gate.payneteasy.com/paynet/api/v2/",
                "endpoint" => "2879",
                "merchant_key" => "1398E8C3-3D93-44BF-A14A-6B82D3579402",
                "merchant_login" => "garan24"
            ],
            "PreauthRequest" => [
                "url" => "https://sandbox.libill.com/paynet/api/v2/",
                "endpoint" => "204",
                "merchant_key" => "DB3C4FE7-1D1B-4106-8E36-1F5EAC807E34",
                "merchant_login" => "eurolego"
            ]
        ],
    ];
}
?>
