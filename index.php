<?php
//print_r(file_get_contents("C:\Users\Affan\Downloads\a12.png"));
// $cFile = '@' . realpath($_FILES['passport']['tmp_name']);

// $post = array('extra_info' => '123456','file_contents'=> $cFile);
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL,"http://52.171.129.143/public/api/kyc_form");
// curl_setopt($ch, CURLOPT_POST,1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//     "Accept: application/json",
//     "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly81Mi4xNzEuMTI5LjE0My9wdWJsaWMvYXBpL2xvZ2luMiIsImlhdCI6MTUyMjI4NTk2OSwiZXhwIjoxNTIyMjg5NTY5LCJuYmYiOjE1MjIyODU5NjksImp0aSI6Inp2ZU1Sc2NRdXhhZDhtMGcifQ.aE5T4Z3MAqrAWK72G_6wSmS9Dki6WwbjYh-1WFtDVkI",
//     "Cache-Control: no-cache"));
// $result=curl_exec ($ch);
// curl_close ($ch);
		require __DIR__ . '/vendor/autoload.php';
		include 'core/funcs.php';
		use \Curl\Curl;
if(isset($_GET['token']) && isset($_GET['userid']) && isset($_GET['username']) && isset($_GET['bind'])){
	if(!empty($_GET['token']) && !empty($_GET['userid']) && !empty($_GET['username']) && !empty($_GET['bind'])){
		\session_start();

		$error = array();
		if(isset($_POST['kyc-submit']) && check_code($_SESSION['xss_code_generate'])){
			if(!empty($_POST['fullname']) && !empty($_POST['street-line-1']) && !empty($_POST['fullname']) && !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['country'])){

			$address = $_POST['street-line-1']." ,".$_POST['street-line-2'];
			//$put = new Curl();
			$auth = "Bearer ".base64_decode($_GET['token']);
			$cFile = '@' . realpath($_FILES['passport']['tmp_name']);

$post = array('broker_id' =>$_GET['bind'],'file_contents'=> $cFile);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://52.171.129.143/public/api/kyc_form");
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/json",
    "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly81Mi4xNzEuMTI5LjE0My9wdWJsaWMvYXBpL2xvZ2luMiIsImlhdCI6MTUyMjI4NTk2OSwiZXhwIjoxNTIyMjg5NTY5LCJuYmYiOjE1MjIyODU5NjksImp0aSI6Inp2ZU1Sc2NRdXhhZDhtMGcifQ.aE5T4Z3MAqrAWK72G_6wSmS9Dki6WwbjYh-1WFtDVkI",
    "Cache-Control: no-cache"));
$result=curl_exec ($ch);
curl_close ($ch);

// 
// 			$target_dir = 'C:\xampp\htdocs\kyc-form\uploads\ ';
// $target_file = $target_dir . basename($_FILES["passport"]["name"]);
// echo $target_file;
// $uploadOk = 1;
// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// $check = getimagesize($_FILES["passport"]["tmp_name"]);
// print_r($check);
// move_uploaded_file($_FILES["passport"]["tmp_name"], $target_file);
			
			//$put->setHeader('Authorization', $auth);
    		//$put->setHeader('Content-Type', 'application/x-www-form-urlencoded');
    		//$put->setHeader('content-type', 'multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW');
    		// $put->setHeader('Accept', 'application/json');
    		// $put->post('http://52.171.129.143/public/api/kyc_form', array(
    		// 	'broker_id'=>$_GET['bind'],
    		// 	'userid'=>$_GET['userid'],
    		// 	'username'=>$_GET['username'],
    		// 	'passport'=>'@'.'C:\xampp\htdocs\kyc-form\uploads\12.png',
    		// 	'fullname'=>$_POST['fullname'],
    		// 	'address'=>$address,
    		// 	'city'=>$_POST['city'],
    		// 	'country'=>$_POST['country'],
    		// 	'state'=>$_POST['state'],
    		// ));
    		// if ($put->error) {
    		// 	$error[1] = json_encode($put);
    		// 	print_r($put);
    		// }
    		// else{
    		// 	$error[2] = json_encode($put->response);
    		// }
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
			<?php } ?>
			<?php if(isset($error[1])){ ?>
  			<div class="alert alert-danger" role="alert">
  				<strong>Oh snap!</strong> <?php echo $error[1]; ?>
			</div>
			<?php } ?>
			<?php if(isset($error[2])){ ?>
  			<div class="alert alert-sucess" role="alert">
  				<strong>Oh snap!</strong> <?php echo $error[2]; ?>
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
					<label for="passport-image">Passport Image</label>
					<input type="file" name="passport" class="form-control-file" required>
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