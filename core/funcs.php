<?php

function xss_code_generate(){
  return $_SESSION['xss_code_generate'] = base64_encode(openssl_random_pseudo_bytes(32));
}

function check_code($token){
  if($_SESSION['xss_code_generate'] == $token){
    unset($_SESSION['xss_code_generate']);
    return true;
  }
  return false;
}
?>