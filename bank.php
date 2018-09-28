<?php
		require __DIR__ . '/vendor/autoload.php';
		include 'core/funcs.php';
		include 'core/countries.php';
		use \Curl\Curl;
if(isset($_GET['token']) && isset($_GET['userid']) && isset($_GET['username']) && isset($_GET['bind'])){
	if(!empty($_GET['token']) && !empty($_GET['userid']) && !empty($_GET['username']) && !empty($_GET['bind'])){
		session_start();
		if(isset($_POST['bnk-submit']) && check_code($_POST['xss_code'])){
			if(!empty($_POST['iban'])){
				$allowed =  array('png','jpg','PNG','JPG','JPEG','jpeg');
				$filenames = array($_FILES['bank_statement']['name']);
				$ext = array(pathinfo($filenames[0], PATHINFO_EXTENSION));
				if($_FILES['bank_statement']['error']==0){
					if(in_array($ext[0], $allowed)){
						if($_FILES['bank_statement']['size'] < 1572864 && $_FILES['bank_statement']['size'] != 0){
							$auth = "Bearer ".base64_decode($_GET['token']);
							/*-- POST --*/
							$fields = array('broker_id' => $_GET['bind'], 'userid'=>$_GET['userid'],'username'=>$_GET['username'],'iban'=>$_POST['iban']);
							$files['statements'] = file_get_contents($_FILES['bank_statement']['tmp_name']);
							$url = "https://sys.pixiubit.com/api/kyc_bank_form";
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
									"X-Requested-With: XMLHttpRequest",
									"Content-Type: multipart/form-data; boundary=" . $delimiter,
									"Content-Length: " . strlen($post_data))
							));
							$response = curl_exec($curl);
							$err = curl_error($curl);
							curl_close($curl);
							if($err){
								$error[1] = "cURL Error #:" . $err;
							}
							else{
								$data = json_decode($response);
								//echo json_encode($response);
								if($data->success == false){
									$error[2] = $data->error;
								}
								else{
									$success[0] = $data->message;
								}
							}
						}
						else{
							$error[0] = "FILE SIZE MUST BE LESS THAN 1.5 MB";
						}
					}
					else{
						$error[0] = "DOCUMENTS ONLY ACCEPTED IN JPG, JPEG, PNG FORMAT";
					}
				}
				else{
					$error[0] = "ERROR IN UPLOADING".json_encode($_FILES['bank_statement']['error']);
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
			'username'=>$_GET['username'],
			'message'=>'kyc-level-2'
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
		.bank_form{
			padding-left: 25px;
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
  					<?php }if(isset($success[0])){ ?>
  						<div class="alert alert-success" role="alert">
  							<strong> <?php echo $success[0]; ?> </strong>
  						</div>
  					<?php }else{?>
						<form class="row bank_form" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<div class="input-group-md">
									<label for="iban">Bank Account IBAN</label>
									<input class="form-control" placeholder="IBAN NUMBER" type="text" name="iban" />
								</div>
								<br/>
								<div class="input-group-md">
									<label for="iban">Submit Bank Account Statement</label>
									<input class="form-control" type="file"  accept="image/*, application/pdf" name="bank_statement" />
									<input type="hidden" class="form-control" name="xss_code" value=<?php echo xss_code_generate(); ?> readonly required>
								</div>
								<br>
								<div class="input-group-md">
									<input type="submit" class="form-control btn btn-primary" name="bnk-submit" value="Submit">
								</div>
								<br>
								<div class="alert alert-danger">
									ONLY <strong>SEPA</strong> REGION BANKING SUPPORTED
								</div>
								<div class="alert alert-danger">
									BANKING STATEMENT MUST NOT BE OLDER THAN <strong>90 DAYS</strong>
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