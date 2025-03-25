<?php 

include ("connections.php");
$name = $address = $email = $password = $cpassword =  "";
$nameErr = $addressErr = $emailErr = $passwordErr = $cpasswordErr = "" ; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = $_POST["address"];
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];
    }

    if (empty($_POST["cpassword"])) {
        $cpasswordErr = "Confirm Password is required";
    } else {
        $cpassword = $_POST["cpassword"];
    }
   
    if($name && $address && $email && $password && $cpassword){
        $check_email = mysqli_query($connections, "SELECT * FROM mytbl WHERE email='$email'");
        $check_email_row = mysqli_num_rows($check_email);

        if($check_email_row > 0){
            $emailErr = "Email is already registered!";
        }else{
            $query = mysqli_query($connections, "INSERT INTO mytbl (name,address,email,password,account_type) VALUES ('$name','$address','$email','$cpassword', '2')");
            echo "<script language = 'javascript'>alert('New record has been inserted!')</script>";
            echo "<script>window.location.href='index.php'</script>";
        }
    }
}
?>



<?php include("nav.php"); ?>

<div class="container">
    <h2>Register</h2>
    <form method="POST" action="<?php htmlspecialchars("PHP_SELF");?>">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $name; ?>">
        <span class="error"><?php echo $nameErr; ?></span>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo $address; ?>">
        <span class="error"><?php echo $addressErr; ?></span>

        <label for="email">Email:</label>
        <input type="text" name="email" value="<?php echo $email; ?>">
        <span class="error"><?php echo $emailErr; ?></span>

        <label for="password">Password:</label>
        <input type="password" name="password" value="<?php echo $password; ?>">
        <span class="error"><?php echo $passwordErr; ?></span>

        <label for="cpassword">Confirm Password:</label>
        <input type="password" name="cpassword" value="<?php echo $cpassword; ?>">
        <span class="error"><?php echo $cpasswordErr; ?></span>

        <input type="submit" value="Submit">
    </form>

    <hr>

    <h2>User List</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Email</th>
            <th>Options</th>
        </tr>

        <?php 
            $view_query = mysqli_query($connections, "SELECT * FROM mytbl");
            while ($row = mysqli_fetch_assoc($view_query)){
                $user_id = $row['id'];
                $db_name = $row["name"];
                $db_address = $row["address"];
                $db_email = $row["email"];
                echo "<tr>
                        <td>$db_name</td>
                        <td>$db_address</td>
                        <td>$db_email</td>
                        <td>
                        <a href='edit.php?id=$user_id'>Update</a> &nbsp;
                        <a href='confirmdelete.php?id=$user_id'>Delete</a>
                        </td>
                    </tr>";
            }
        ?>
    </table>
</div>

<hr>

<div class="footer">
    <?php 
        $Paul = "Paul";
        $Mica = "Mica";
        $Kaye = "Kaye";
        $names = array($Paul, $Mica, $Kaye);
        foreach($names as $display_names){
            echo $display_names . "<br>";
        }
    ?>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
    }

    h1, h2 {
        text-align: center;
        color: #333;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 10px;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 20px auto;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #c80036;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #a0002b;
    }

    .error {
        color: red;
        font-size: 12px;
        display: block;
        margin-top: 5px;
        margin-bottom: 15px; /* Added margin at the bottom to ensure error messages have enough space */
    }

    table {
        width: 80%;
        margin: 30px auto;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    a {
        color: #4CAF50;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .footer {
        text-align: center;
        padding: 10px;
        background-color: #333;
        color: white;
    }

    nav {
        background-color: #333;
        padding: 10px 0;
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        text-align: right;
    }

    nav ul li {
        display: inline;
        margin-left: 20px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        padding: 10px;
    }

    nav ul li a:hover {
        background-color: #4CAF50;
        border-radius: 4px;
    }

</style>
