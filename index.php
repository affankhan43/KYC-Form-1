<?php
		require __DIR__ . '/vendor/autoload.php';
		include 'core/funcs.php';
		include 'core/countries.php';
		use \Curl\Curl;
if(isset($_GET['token']) && isset($_GET['userid']) && isset($_GET['username']) && isset($_GET['bind'])){
	if(!empty($_GET['token']) && !empty($_GET['userid']) && !empty($_GET['username']) && !empty($_GET['bind'])){
		session_start();
		if(isset($_POST['kyc-submit']) && check_code($_POST['xss_code'])){
			if(!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['street-line-1']) && !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['country']) && !empty($_POST['phone']) && !empty($_POST['zip']) && !empty($_POST['passport_no']) && !empty($_POST['passport_exp']) && !empty($_POST['passport_isu'])){
				$allowed =  array('png' ,'jpg','PNG','JPG','JPEG','jpeg');
				$filenames = array($_FILES['passports']['tmp_name'],$_FILES['passport_selfie']['tmp_name'],$_FILES['statement_bill']['tmp_name']);
				$ext = array(pathinfo($filenames[0], PATHINFO_EXTENSION),pathinfo($filenames[1], PATHINFO_EXTENSION),pathinfo($filenames[2], PATHINFO_EXTENSION));
				if(in_array($ext[0], $allowed) || in_array($ext[1], $allowed) || in_array($ext[2], $allowed)){
					$address = $_POST['street-line-1']." ,".$_POST['street-line-2'];
					$auth = "Bearer ".base64_decode($_GET['token']);

					/*-- POST --*/
					$fields = array('broker_id' => $_GET['bind'], 'userid'=>$_GET['userid'],'username'=>$_GET['username'],'first_name'=>$_POST['first_name'],'last_name'=>$_POST['last_name'],'address'=>$address,'country_of_residence'=>$_POST['country'],'city'=>$_POST['city'],'state'=>$_POST['state'],'phone'=>$_POST['phone'],'postal_code'=>$_POST['zip'],'nationality'=>$_POST['nationality'],'passport_no'=>$_POST['passport_no'],'passport_exp'=>$_POST['passport_exp'],'passport_isu'=>$_POST['passport_isu'],'employment'=>$_POST['employment']);
					$files['passport'] = file_get_contents($_FILES['passports']['tmp_name']);
					$files['passport_selfie'] = file_get_contents($_FILES['passport_selfie']['tmp_name']);
					$files['statments'] = file_get_contents($_FILES['statement_bill']['tmp_name']);
					$url = "https://sys.pixiubit.com/api/kyc_form";
					$curl = curl_init();
					$boundary = uniqid();
					$delimiter = '-------------' . $boundary;
					$post_data = build_data_files($boundary, $fields, $files);
					curl_setopt_array($curl, array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POST => 1,
						CURLOPT_POSTFIELDS => $post_data,
						CURLOPT_HTTPHEADER => array(
							"Authorization: ".$auth."",
							"Content-Type: multipart/form-data; boundary=" . $delimiter,
							"Content-Length: " . strlen($post_data)),
							"Accept: application/json"
					));
					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);
					if($err){
						$error[1] = "cURL Error #:" . $err;
					}
					else{
						$data = json_decode($response);
						if($data->success == false){
							//print_r($data);
							$error[2] = $data->error;
						}
						else{
							//print_r($data);
							$success[0] = $data->message;
						}
					}
				}
				else{
					$error[0] = "FILES ONLY ACCEPTED IN JPG, JPEG, PNG FORMAT";
				}
			}
			else{
				$error[0] = "Fill All Fields";
			}
		}
		$user_check = new Curl();
		$user_check->setHeader('Authorization','Bearer '.base64_decode($_GET['token']));
		$user_check->setHeader('X-Requested-With', 'XMLHttpRequest');
		$user_check->post('https://sys.pixiubit.com/api/user_check',array(
        	'broker_id'=>$_GET['bind'],
			'userid'=>$_GET['userid'],
			'username'=>$_GET['username']
        ));
        if ($user_check->error) {
          $check_msg[0] = "Authentication Failed";
        }
        else{
        	$checker = $user_check->response;
        	if(isset($checker->success)){
        		if($checker->success == true){
        			$form_avail = true;
        		}
        		else{
        			$check_msg[0] = $checker->message;
        		}
        	}
        	else{
        		$check_msg[0] = "Under Maintenance.";
        	}
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
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<title>Alladin | KYC</title>
	<style type="text/css">
		body{
			font-family: 'Roboto', sans-serif;
		}
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
			margin-bottom: 0.15rem;
		}
		.border-line{
			border-right: solid 2px #c8c8c8;
		}
	</style>
</head>
  <body>
  	<div class="container">
  		<div class="col-md-12 kyc-form">
  			<?php if((isset($form_avail) && $form_avail==true) || isset($error[0]) || isset($error[1]) || isset($error[2]) || isset($success[0])){
  					if(isset($error[0])){ ?>
  						<div class="alert alert-danger" role="alert">
  							 <?php echo $error[0]; ?>
  						</div>
  					<?php }elseif(isset($error[1])){ ?>
  						<div class="alert alert-warning" role="alert">
  							 <?php echo $error[1]; ?>
  						</div>
  					<?php }elseif(isset($error[2])){ ?>
  						<div class="alert alert-success" role="alert">
  							<strong> <?php echo $error[2]; ?> </strong>
  						</div>
  					<?php }elseif(isset($success[0])){ ?>
  						<div class="alert alert-success" role="alert">
  							<strong> <?php echo $success[0]; ?> </strong>
  						</div>
  					<?php }else{?>
						<form class="row" method="post" enctype="multipart/form-data">
							<div class="col-md-6 border-line">
								<h2>Personal Information</h2>
								<br>
								<!-- First Name & Last Name -->
								<div class="row">
									<div class="col-md-6 form-group">
										<label for="first_name">First Name</label>
										<input type="text" class="form-control" name="first_name" placeholder="First Name" required>
									</div>
									<div class="col-md-6 form-group">
										<label for="last_name">Last Name</label>
										<input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
									</div>
								</div>

								<!-- Nationality & Country -->
								<div class="row">
									<div class="col-md-6 form-group">
										<label for="nationality">Nationality</label>
										<select class="form-control" name="nationality">
											<?php for ($nat=0; $nat < sizeof($nationality_list); $nat++) { 
												echo "<option>".$nationality_list[$nat].'</option>';
											} ?>
										</select>
									</div>
									<div class="col-md-6 form-group">
										<label for="nationality">Country of Residence</label>
										<select class="form-control" name="country">
											<?php for ($co=0; $co < sizeof($countries_list); $co++) { 
												echo "<option>".$countries_list[$co].'</option>';
											} ?>
										</select>
									</div>
								</div>

								<!-- Address & City -->
								<div class="row">
									<div class="form-group col-md-12">
										<label for="address">Address</label>
										<div class="row">
											<div class="col-md-6">
												<input type="text" class="form-control" name="street-line-1" placeholder="Street Line 1">
												<br>
												<input type="text" class="form-control" name="street-line-2" placeholder="Street Line 2">
											</div>
											<div class="col-md-6">
												<input type="text" class="form-control" name="city" placeholder="City" required>
												<br>
												<input type="text" class="form-control" name="state" placeholder="State/Province" required>
			  									
			  									<input type="hidden" class="form-control" name="xss_code" value=<?php echo xss_code_generate(); ?> readonly required>
											</div>
										</div>
									</div>
								</div>

								<!-- Phone Number & Employment status -->
								<div class="row">
									<div class="form-group col-md-6">
										<label for="address">Phone #</label>
										<input type="text" pattern="^[0-9+()]*$" class="form-control" name="phone" placeholder="Phone #" required>
									</div>
									<div class="form-group col-md-6">
										<label for="employment">Employment Status</label>
										<select class="form-control" name="employment" required>
											<option>Employed</option>
											<option>Self-Employment</option>
										</select>
									</div>
									<div class="form-group col-md-6">
										<label for="address">ZIP Code</label>
										<input type="text" pattern="^[0-9]*$" class="form-control" name="zip" placeholder="ZIP Code" required>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<h2>Identity Information</h2>
								<br>
								<div class="row">
									<div class="col-md-12 form-group">
										<label for="address">Passport No.</label>
										<input type="text" class="form-control" name="passport_no" placeholder="Passport Number" required>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-6">
										<label for="address">Passport Issue Date</label>
										<input type="date" class="form-control" name="passport_isu" required>
			  						</div>
			  						<div class="form-group col-md-6">
			  							<label for="address">Passport Expiry Date</label>
			  							<input type="date" class="form-control" name="passport_exp" required>
			  						</div>
			  					</div>
			  					<div class="row">
			  						<div class="form-group col-md-12">
			  							<label for="passport">Passport Image</label>
			  							<input type="file" accept="image/*" name="passports" class="form-control-file" required>
			  						</div>
			  					</div>
			  					<div class="row">
									<div class="form-group col-md-12">
									<label for="passport-image">Selfie With Passport</label>
									<input type="file" accept="image/*" name="passport_selfie" class="form-control-file" required>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-12">
									<label for="passport-image">Utility Bill / Bank Statment <span class="required">(Not More Than 3 Months Old)</span></label>
									<input type="file" accept="image/*" name="statement_bill" class="form-control-file" required>
									</div>
								</div>				
							</div>

							<div class="col-md-12">
								<div class="form-group col-md-3 offset-9">
									<input type="submit" class="form-control btn btn-primary" name="kyc-submit" value="Submit">
								</div>
							</div>
						</form>
					<?php } ?>
				<?php }elseif(isset($check_msg[0])){ ?>
					<div class="alert alert-warning" role="alert">
						<?php echo $check_msg[0]; ?>
					</div>
				<?php }else{ ?>
					<div class="alert alert-warning" role="alert">
						<?php echo "Under Maintenance.."; ?>
					</div>
				<?php } ?>

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