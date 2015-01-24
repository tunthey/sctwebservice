<?php
require_once("includes/component_loader.php");

		
		if($db)
		{
            $user = array();
            $stmt = $db->prepare("SELECT * from mytest");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                foreach ($result as $value) {
                   $user[] = array('user'=>$value);
                }
            }
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($user);
    	}
        
?>