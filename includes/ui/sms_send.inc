<?php
/*
class callWSDL{
	var $wsdl_url;
	
	function callWSDL(){
		$this->wsdl_url='http://corporate.visionon-tech.com/vendorsms/pushsms.aspx?';
	}
	
	function SendBulkSMS($destination, $message){
		try{
		    $sClient = new SoapClient('http://corporate.visionon-tech.com/vendorsms/pushsms.aspx?');	
			$params->Source = 'DUBAIMOBILE';
			$params->Destination = $destination; 
			$params->Mask = 'farhan1'; 
			$params->Message = $message; 
			$params->UniCode=  0; 
			$params->Password = 'pass123@%'; 
		    $response = $sClient->SendBulkSMS($params);
		var_dump($response);
		} catch(SoapFault $e){
		var_dump($e);
		}
	}
}*/

function sms_send($Mobile_No, $Text)
{

    $id = "farhan1";

    $pass = "@@DMOBILE@@";

    $sid = "DUBAIMOBILE";

    $to = "$Mobile_No";

    $MobileNumber = get_company_pref('MobileNumber');

    $message = $Text;
    $message = htmlspecialchars_decode($message, ENT_NOQUOTES);
    $message = urlencode($message);
    if($MobileNumber)
        $data = "apikey=7c756ecb-0540-491a-ab57-a739d3b4a0d4&clientid=f0362ba2-031f-4e9b-8c58-db18bd21ab94&user=".$id."&password=".$pass."&msisdn=".$to.",$MobileNumber"."&sid=".$sid."&msg=".$message."&fl=0";
    else
        $data = "apikey=7c756ecb-0540-491a-ab57-a739d3b4a0d4&clientid=f0362ba2-031f-4e9b-8c58-db18bd21ab94&user=".$id."&password=".$pass."&msisdn=".$to."&sid=".$sid."&msg=".$message."&fl=0";
  
    $ch = curl_init('http://corporate.visionon-tech.com/vendorsms/pushsms.aspx?');

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch); //This is the result from SMS4CONNECT

    curl_close($ch);

}

?>