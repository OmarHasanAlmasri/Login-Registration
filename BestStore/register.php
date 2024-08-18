<?php
include "layout/header.php";

//logged in user are redirected to the home page
if(isset($_SESSION["email"])){
    header("location:/BestStore/index.php");
    exit;
}



$first_name="";
$last_name="";
$email="";
$phone="";
$address="";

$first_name_error="";
$last_name_error="";
$email_error="";
$phone_error="";
$address_error="";
$password_error="";
$confirm_password_error="";
$error=false;

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $first_name=$_POST["first_name"];
    $last_name=$_POST["last_name"];
    $email=$_POST["email"];
    $phone=$_POST["phone"];
    $address=$_POST["address"];
    $password=$_POST["password"];
    $confirm_password=$_POST["confirm_password"];

    /************************************ validate first_name ***************************************/ 
    if( empty($first_name )){
        $first_name_error="First Name is required";
        $error=true;
    }
    /************************************ validate last_name  ***************************************/ 
    if(empty( $last_name )){
        $last_name_error="Last Name is required";
        $error=true;
    }
    /************************************ validate email ***************************************/ 
    //check email format
    if( !filter_var($email,FILTER_VALIDATE_EMAIL )){
        $email_error="Email Format is Not valid";
        $error=true;
    }

    include "tools/db.php";
    $dbConnection=getDatabaseConnection();
    
    $statment=$dbConnection->prepare("SELECT id FROM users WHERE email=?");

    //Bind variables to the prepared statments as parameter
    $statment->bind_param("s",$email);

    //execute statment
    $statment->execute();

    //check if is the email already in the database 
    $statment->store_result();
    if( $statment->num_rows()>0 ){
        $email_error="Email is already used";
        $error=false;
    }

    //close this statment otherwise we cannot prepare anther statment
    $statment->close();

    /************************************ validate phone  ***************************************/ 
    //Define a regex for phone format
    //Optional country code (+ or 00 followed by 1 to 3 digits)
    //Optional space or dash seperator
    //Number  (7 to 12 digit) 
    if( !preg_match("/^(\+|00\d{1,3})?[-]?\d{7,12}$/",$phone )){
        $phone_error="Phone Format is Not valid";
        $error=True;
    }

    /************************************ validate password  ***************************************/ 
    if( strlen($password)<6 ){
        $password_error="password must have at lest 6 charecters";
        $error=True;
    }

    /************************************ validate confirm_password  ***************************************/ 
    if( $confirm_password != $password ){
        $confirm_password_error="Password and Confirm the passwod do Not match";
        $error=True;
    }

    if($error){
        $password=password_hash($password,PASSWORD_DEFAULT);
        $created_at=date("Y-m-d H:i:s");
        
        //Let use prepared statments to avoid "sql injection attacks"
        $statment=$dbConnection->prepare(
            "INSERT INTO users (first_name,last_name,email,phone ,address,password,created_at )"
            ."VALUES(?,?,?,?,?,?,?)");

            //Bind variables to the prepared statment as parameters
            $statment->bind_param("sssssss",$first_name,$last_name,$email,$phone,$address,$password,$created_at);

            //execute statment
            $statment->execute();
            
            $insert_id=$statment->insert_id;
            $statment->close();


        /***********************A new account is created *************************/

        //save session data
        $_SESSION["id"]=$insert_id;
        $_SESSION["first_name"]=$first_name;
        $_SESSION["last_name"]=$last_name;
        $_SESSION["email"]=$email;
        $_SESSION["phone"]=$phone;
        $_SESSION["address"]=$address;
        $_SESSION["created_at"]=$created_at;

        //Redirect user to the home page 
        header("location:/BestStore/index.php");
        exit;


    }



}

?>

<div class="continar py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Register</h2>
            <hr>
            <form  method="post">
                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">First Name*</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="first_name" value="<?= $first_name ?>">
                        <span class="text-danger"><?= $first_name_error ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Last Name*</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="last_name" value="<?= $last_name ?>">
                        <span class="text-danger"><?=$last_name_error ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Email*</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="email" value="<?= $email ?>">
                        <span class="text-danger"><?= $email_error?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Phone*</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="phone" value="<?= $phone ?>">
                        <span class="text-danger"><?= $phone_error ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Address</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="address" value="<?= $address ?>">
                        <span class="text-danger"><?= $address_error ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Password*</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" name="password" value="">
                        <span class="text-danger"><?= $password_error ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="" class="col-sm4 col-form-label">Confirm Password*</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" name="confirm_password" value="">
                        <span class="text-danger"><?= $confirm_password_error ?></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-4 col-sm-4 d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    <div class="col-sm-4 d-grid">
                        <a href="/BestStore/index.php" class="btn btn-outline-primary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




<?php
include "layout/footer.php";
?>