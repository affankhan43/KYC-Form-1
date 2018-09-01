<?php
		require __DIR__ . '/vendor/autoload.php';
		include 'core/funcs.php';
		include 'core/countries.php';
		use \Curl\Curl;
if(isset($_GET['token']) && isset($_GET['userid']) && isset($_GET['username']) && isset($_GET['bind'])){
	if(!empty($_GET['token']) && !empty($_GET['userid']) && !empty($_GET['username']) && !empty($_GET['bind'])){
		session_start();
		$error = array();
		if(isset($_POST['bnk-submit']) && check_code($_POST['xss_code'])){
			if(!empty($_POST['iban'])){
				$auth = "Bearer ".base64_decode($_GET['token']);
				/*-- POST --*/
				$fields = array('broker_id' => $_GET['bind'], 'userid'=>$_GET['userid'],'username'=>$_GET['username'],'iban'=>$_POST['iban']);
				$filenames = array($_FILES['bank_statement']['tmp_name']);
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
						$error[2] = $data->error;
					}
					else{
						$success[0] = $data->message;
					}
				}
			}
			else{
				$error[0] = "Fill All Fields";
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
			<?php }elseif(isset($success[0])){ ?>
			<div class="alert alert-success" role="alert">
				<strong> <?php echo $success[0]; ?> </strong>
			</div>
			<?php }if(!isset($success[0])){ ?>
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
					<div class="input-group-md">
						<input type="submit" class="form-control btn btn-primary" name="bnk-submit" value="Submit">
					</div>
				</div>
			</form>
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