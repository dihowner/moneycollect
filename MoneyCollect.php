<?php
class MoneyCollect {
    private $base_url, $live_publicKey, $live_privateKey, $test_publicKey, $test_privateKey, $referenceId, $header;
    public function __construct() {
        $this->base_url = "https://api.moneycollect.com/api/services/v1";
        $this->referenceId = date('YmdHi').mt_rand(11111, 99999);
        // Please use .env to save keys
        // For production
        $this->live_publicKey = "";
        $this->live_privateKey = "";
        // For testing sake...
        $this->test_publicKey = "";
        $this->test_privateKey = "";

        $this->header = array(
            "Content-Type: application/json",
            "Authorization: Bearer ". $this->test_privateKey  
        );
    }

    public function create_session($customer_name, $phone, $email, $amount) {
        $notifyUrl = 'http://moneycollect.local/session/notify.php';
        $returnUrl = 'http://moneycollect.local/session/return.php';
        $cancelUrl = 'http://moneycollect.local/session/cancel.php';

        $data = array(
            "amountTotal" => (float) $this->convert_to_usd($amount),
            "customer_name" => $customer_name,
            "currency" => "USD",
            "orderNo" => $this->referenceId,
            "confirmationMethod" => "automatic",
            "notifyUrl" => $notifyUrl,
            "returnUrl" => $returnUrl,
            "cancelUrl" => $cancelUrl,
            'paymentMethodTypes' => [
                'card'
            ],
        );
        $this->logs($this->referenceId);
        $response = $this->push_to_vendor("POST", $this->base_url . "/checkout/session/create", json_encode($data), $this->header);
        $this->logs($response);
        return $response;
    }

    public function retrieve_session($id) {
        return $this->push_to_vendor("GET", $this->base_url . "/checkout/session/".$id, "", $this->header);
    }
    
    public function create_payment($customer_name, $phone, $email, $amount) {
        $data = array(
            "amount" => (float) $this->convert_to_usd($amount),
            "customer_name" => $customer_name,
            "currency" => "USD",
            "orderNo" => $this->referenceId,
            "confirmationMethod" => "automatic"
        );
        return $this->push_to_vendor("POST", $this->base_url . "/payment/create", json_encode($data), $this->header);
    }

    public function push_to_vendor($request_type, $request_url, $data, $header) {
        $curl = curl_init();

        $curl_options = array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_type,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_VERBOSE => 1,
            CURLOPT_POSTFIELDS => $data
        );
        curl_setopt_array($curl, $curl_options);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);
        if ($error) {
            $this->logs($error);
            return $error;
        } else {
            return $response;
        }
    }

    public function logs($data) {
        $data = is_array($data) ? json_encode($data) : $data;
        file_put_contents('moneycollect.txt', $data . "\n", FILE_APPEND);
    }

    public function revert_from_usd($amount) {
        return (float) $this->convert_to_usd($amount) / 100;
    }

    public function convert_to_usd($amount) {
        return (float) $amount * 100;
    }
}
?>