<?php
print '<pre>';

print_r(client_trades());

print_r(open_trades());
 
print_r(closed_trades());
 
 function open_trades()
    {       
		$ch = curl_init("https://api.4xsolutions.com/tradereplicator/api/mastertrades");
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
        curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
        curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false );                                                                         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',
			'Accept: application/json',
			'API-KEY: ##########',
        )); 
		
        $result = curl_exec($ch);
        $response = json_decode($result, true); 
		
        curl_close($ch);
        return($response); 
    }
	
	function closed_trades()
    {       
		$ch = curl_init("https://api.4xsolutions.com/tradereplicator/api/mastertrades?filter.ishistory=true");
        
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
        curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
        curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false );                                                                         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type:Application/json',
			'Accept: application/json',
			'API-KEY: ##########',
        )); 
		
        $result = curl_exec($ch);
        $response = json_decode($result, true); 
		
        curl_close($ch);
        return($response); 
    }

function client_trades()
{
    $ch = curl_init("https://api.4xsolutions.com/tradereplicator/api/clienttrades?filter.ishistory=true");
    curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
    curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:Application/json',
        'Accept: application/json',
        'API-KEY: ##########',
    ));

    $result = curl_exec($ch);
    $response = json_decode($result, true);

    curl_close($ch);
    return($response);
}

?>	
	
 