<?php
$process="true";
if(isset($_SESSION["expense"])){
	
	$buy = $_SESSION["expense"];

//////////////////////////////////
		if($process==true){
			$sell = new OperationData();
			$sell->user_id = $_SESSION["user_id"];

			$sell->total = $_POST["total"];
			$tot=$_POST["total"];
 			$totalGen=$_POST["totalGen"];
        
            $liq=$tot - $totalGen;

            if($liq==0){
            $sell->status = 1;
            }else{
            $sell->status = 0;
            }
            $sell->note = $_POST["note"];
			

 			$s = $sell->addBuyS();

 			
	        
		foreach($buy as  $c){


			 $op = new OperationDetailData();

			 $op->product_id = $c["idCon"] ;
			 $op->operation_type_id=1; // 1 - entrada
			 $op->sell_id=$s[1];
			 $op->q= $c["q"] ;
             $op->price= $c["cost"];
			 $op->is_oficial = 1;
			
             $add = $op->add_buy();		 		

			unset($_SESSION["expense"]);
			setcookie("selled","selled");
		}

		if(isset($_SESSION["expensePaymentTypes"])){
		 $pay = $_SESSION["expensePaymentTypes"];


		 foreach($pay as  $p){

			 $op = new OperationDetailData();
			 $op->idType = $p["idType"] ;
			 $op->buyId=$s[1];
			 $op->money= $p["money"];
			


			 $add = $op->addPayB();			 		

			unset($_SESSION["expensePaymentTypes"]);
			setcookie("selled","selled");
		}
	}


//print "<script>window.location='index.php?view=expenses';</script>";
		}
	}




?>
