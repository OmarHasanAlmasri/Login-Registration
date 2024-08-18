<?php
include "layout/header.php";

//check if the user is logged in,if yes then redirct him to the home page 
if(isset($_SESSION["email"])){
    header("location:/BestStore/index.php");
    exit;
}

$email="";
$error="";
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email=$_POST["email"];
    $password=$_POST["password"];

    if(empty($email) || empty($password)){
        $error="Email and password are required";
    }else{
        
    include "tools/db.php";
    $dbConnection=getDatabaseConnection();
    
    $statment=$dbConnection->prepare("SELECT id , first_name , last_name, phone , password , created_at FROM users WHERE email=?");

    //Bind variables to the prepared statments as parameter
    $statment->bind_param("s",$email);

    //execute statment
    $statment->execute();

    //bind result variables
    $statment->bind_result($id,$first_name,$last_name,$phone,$stored_password,$created_at);

    //fetch values
    if($statment->fetch()){
        if(password_verify($password,$stored_password)){
            //password is correct

            //Store data in session variables
            $_SESSION["id"]=$id;
            $_SESSION["first_name"]=$first_name;
            $_SESSION["last_name"]=$last_name;
            $_SESSION["email"]=$email;
            $_SESSION["phone"]=$phone;
            $_SESSION["created_at"]=$created_at;

            //Redirect user to the home page
            header("location:/BestStore/index.php");
            exit;
        }
    }
    $statment->close();
    $error="Email or Password invalid";

    }

}



?>


<div class="container py-5">
    <div class="mx-auto border shadow p-4" style="width:400px">
        <h2 class="text-center mb-4">Login</h2>
        <hr>
        <?php if(!empty($error)){ ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><?= $error ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>       
        <?php } ?>



        <form  method="post">
            <div class="mb-3">
                <label  class="form-label">Email</label>
                <input value="<?= $email ?>" name="email" class="form-control">
            </div>
            <div class="mb-3">
                <label  class="form-label">Password</label>
                <input type="password" class="form-control" name="password" >
            </div>
            <div class="rowcd-mb-3">
                <div class="col d-grid">
                    <button type="submit" class="btn btn-primary">Log in</button>
                </div>
                <div class="col d-grid">
                    <a href="/BestStore/index.php" class="btn btn-outline-primary">Cancel</a>

                </div>

            </div>
        </form>

    </div>
</div>





<?php
include "layout/footer.php";
?>