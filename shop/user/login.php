<?php 
$email = $password = "";
$emailErr = $passwordErr = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty($_POST["email"])){
        $emailErr = "Email is required!";
    } else{
        $email = $_POST["email"];
    }

    if(empty($_POST["password"])){
        $passwordErr = "Password is required!";
    } else{
       $password = $_POST["password"];
    }
    
    if ($email && $password){
        include("connections.php");
        $check_email = mysqli_query($connections, "SELECT * FROM mytbl WHERE email = '$email'");
        $check_email_row = mysqli_num_rows($check_email);
        if($check_email_row > 0){
            while($row = mysqli_fetch_assoc($check_email)){
                $db_password = $row["password"];
                $db_account_type = $row["account_type"];
                
                if ($password == $db_password){
                    if ($db_account_type == "1"){
                        echo "<script>window.location.href='admin'</script>";
                    }else{
                        echo "<script>window.location.href='user'</script>";
                    }
                }else{
                    $passwordErr = "Password is incorrect!";
                }
            }
        } else{
            $emailErr = "Email is not registered!";
        }
    }
}
?>


<div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="email" value="<?php echo $email;?>" placeholder="Email">
        <br>
        <span class="error"><?php echo $emailErr; ?></span>
        <br>

        <input type="password" name="password" value="<?php echo $password;?>" placeholder="Password">
        <br>
        <span class="error"><?php echo $passwordErr; ?></span>
        <br>

        <input type="submit" value="Login">
    </form>
</div>


<style>
    
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .login-container {
        background-color: white;
        padding: 30px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }

    .error {
        color: red;
        font-size: 12px;
    }

</style>