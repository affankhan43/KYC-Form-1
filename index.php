<?php
		require __DIR__ . '/vendor/autoload.php';
		include 'core/funcs.php';
		use \Curl\Curl;
if(isset($_GET['token']) && isset($_GET['userid']) && isset($_GET['username']) && isset($_GET['bind'])){
	if(!empty($_GET['token']) && !empty($_GET['userid']) && !empty($_GET['username']) && !empty($_GET['bind'])){
		session_start();
		$error = array();
		if(isset($_POST['kyc-submit'])){
			if(check_code($_POST['xss_code'])){ echo "string";}
			$error[0]=check_code($_POST['xss_code']);
		// 	if(!empty($_POST['fullname']) && !empty($_POST['street-line-1']) && !empty($_POST['fullname']) && !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['country']) && !empty($_POST['phone']) && !empty($_POST['zip'])){
		// 		$address = $_POST['street-line-1']." ,".$_POST['street-line-2'];
		// 		$auth = "Bearer ".base64_decode($_GET['token']);
		// 	/*-- POST --*/
		// 	$fields = array('broker_id' => $_GET['bind'], 'userid'=>$_GET['userid'],'username'=>$_GET['username'],'fullname'=>$_POST['fullname'],'address'=>$address,'country'=>$_POST['country'],'city'=>$_POST['city'],'state'=>$_POST['state'],'phone'=>$_POST['phone'],'postal_code'=>$_POST['zip']);
		// 	$filenames = array($_FILES['passport']['tmp_name'],$_FILES['passport_selfie']['tmp_name'],$_FILES['bill']['tmp_name']);
		// 	$files['passport'] = file_get_contents($_FILES['passport']['tmp_name']);
		// 	$files['passport_selfie'] = file_get_contents($_FILES['passport_selfie']['tmp_name']);
		// 	$files['bill'] = file_get_contents($_FILES['bill']['tmp_name']);
		// 	$url = "https://sys.pixiubit.com/api/kyc_form";
		// 	$curl = curl_init();
		// 	$boundary = uniqid();
		// 	$delimiter = '-------------' . $boundary;
		// 	$post_data = build_data_files($boundary, $fields, $files);
		// 	curl_setopt_array($curl, array(
		// 		CURLOPT_URL => $url,
		// 		CURLOPT_RETURNTRANSFER => 1,
		// 		CURLOPT_MAXREDIRS => 10,
		// 		CURLOPT_TIMEOUT => 30,
  // 				CURLOPT_CUSTOMREQUEST => "POST",
		// 		CURLOPT_POST => 1,
		// 		CURLOPT_POSTFIELDS => $post_data,
		// 		CURLOPT_HTTPHEADER => array(
		// 			"Authorization: ".$auth."",
		// 			"Content-Type: multipart/form-data; boundary=" . $delimiter,
		// 			"Content-Length: " . strlen($post_data)),
		// 	));
		// 	$response = curl_exec($curl);
		// 	$err = curl_error($curl);
		// 	curl_close($curl);
		// 	if($err){
		// 		$error[1] = "cURL Error #:" . $err;
		// 	}
		// 	else{
		// 		$data = json_decode($response,true);
		// 		print_r($data);
		// 		if($data['success'] == false){
		// 			$error[2] = $data['error'];
		// 		}
		// 		else{
		// 			$error[3] = $data['message'];
		// 		}
		// 	}
		// }
		// else{
		// 	$error[0] = "Fill All Fields";
		// }
	}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Pixiubit | KYC</title>
    <style type="text/css">
    	.kyc-form{
    		margin: 0 auto;
    		overflow: hidden;
    		padding: 10px 0;
    		align-items: center;
    		justify-content: space-around;
    		float: none;
    	}
    	.form-group>label{
    font-weight: bold;
    font-family:  sans-serif;
    	}
    </style>
  </head>
  <body>
  	<div class="container">
  		<div class="col-md-6 kyc-form">
  			<?php if(isset($error[0])){ ?>
  			<div class="alert alert-danger" role="alert">
  				<strong>Oh snap!</strong> <?php echo $error[0]; ?>
			</div>
			<?php }elseif(isset($error[1])){ ?>
  			<div class="alert alert-warning" role="alert">
  				<strong>Oh snap!</strong> <?php echo $error[1]; ?>
			</div>
			<?php }elseif(isset($error[2])){ ?>
  			<div class="alert alert-success" role="alert">
  				<strong> <?php echo $error[2]; ?> </strong>
			</div>
			<?php }elseif(isset($error[3])){ ?>
  			<div class="alert alert-success" role="alert">
  				<strong> <?php echo $error[2]; ?> </strong>
			</div>
			<?php } ?>
  			<form method="post" enctype="multipart/form-data">
  				<div class="form-group">
  					<label for="fullname">Full Name</label>
  					<input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
  				</div>

				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" class="form-control" name="street-line-1" placeholder="Street Line 1">
					<br>
					<input type="text" class="form-control" name="street-line-2" placeholder="Street Line 2">
					<br>
					<input type="text" class="form-control" name="city" placeholder="City" required>
					<br>
					<input type="text" class="form-control" name="state" placeholder="State/Province" required>
					<br>
					<input type="text" class="form-control" name="country" placeholder="Country" required>
					<input type="hidden" class="form-control" name="xss_code" value=<?php echo xss_code_generate(); ?> readonly required>
				</div>
				<div class="form-group">
					<label for="address">Phone #</label>
					<input type="text" pattern="^[0-9+()]*$" class="form-control" name="phone" placeholder="Phone #">
				</div>
				<div class="form-group">
					<label for="address">Zip Code</label>
					<input type="text" pattern="^[0-9]*$" class="form-control" name="zip" placeholder="Area Zipcode">
				</div>
				<div class="form-group">
					<label for="passport-image">Selfie With Passport</label>
					<input type="file" name="passport_selfie" class="form-control-file" required>
				</div>
				<div class="form-group">
					<label for="passport-image">Passport Image</label>
					<input type="file" name="passport" class="form-control-file" required>
				</div>
				<div class="form-group">
					<label for="passport-image">Paid Bill</label>
					<input type="file" name="bill" class="form-control-file" required>
				</div>
  
				<div class="form-group">
					<input type="submit" class="form-control btn btn-primary" name="kyc-submit" value="Submit">
				</div>
			</form>
		</div>
	</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
<?php } }?>