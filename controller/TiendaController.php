<?php

//require 'third-party/vendor/autoload.php';
//require 'third-party/sdk-php-master/src/MercadoPago/MercadoPagoConfig.php';
//require 'third-party/sdk-php-master/src/MercadoPago/Client/Preference/PreferenceClient.php';

//use MercadoPago\Client\Preference\PreferenceClient;
//use MercadoPago\MercadoPagoConfig;


class TiendaController{
//    private $userModel;
//    private $renderer;
//
//    public function __construct($userModel, $renderer) {
//        $this->userModel = $userModel;
//        $this->renderer = $renderer;
//    }
//
//    public function comprar() {
//        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//            $precio = $_POST['precio'];
//            $trampitas = $_POST['trampitas'];
//            MercadoPagoConfig::setAccessToken("TEST-7288339780727405-110710-82460d9cbad1bb181d0f00e3e23f6a3a-331280944");
//            $preferenceClient = new PreferenceClient();
//            $preferenceData = [
//                "items" => [
//                    [
//                        "title" => "Trampitas",
//                        "quantity" => (int)$trampitas,
//                        "currency_id" => "ARS",
//                        "unit_price" => (int)$precio
//                    ]
//                ]
//            ];
//
//            try {
//                $preference = $preferenceClient->create($preferenceData);
//                echo "MercadoPago Response: " . json_encode($preference, JSON_PRETTY_PRINT);
//                $preferenceId = $preference->id;
//                $sandboxPaymentUrl = $preference->sandbox_init_point . "?preference_id=" . $preferenceId;
//                header("Location: $sandboxPaymentUrl");
//                exit();
//            } catch (Exception $e) {
//                echo "An error occurred: " . $e->getMessage();
//                echo "Exception Details: " . var_export($e, true);
//            }
//        }
//        $this->renderer->render('home');
//    }

}