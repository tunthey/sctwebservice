<?php
require_once("includes/component_loader.php");

		
		if($db)
		{
            $courses = array();
            $stmt = $db->prepare("SELECT * from courses");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0)
            {
                foreach ($result as $value) {
                   $courses[] = array('courses'=>$value);
                }
            }
        header('Content-type: application/json');
       // header('Access-Control-Allow-Origin: *');
        echo $_GET['callback'] . '('.json_encode($courses).')';
      //  echo json_encode($user);
    	}
        
?>