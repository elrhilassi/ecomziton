<?php 
// $con=mysqli_connect('localhost','root','','ecommerce_1');
$con = new mysqli('localhost','root','','store');
if(!$con){
    die(mysqli_error($con));
}




?>