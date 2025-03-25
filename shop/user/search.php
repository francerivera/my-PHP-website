<?php

$search = $searchErr = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(empty($_POST["search"])) {

        $searchErr = "Required!";

    }else{

        $search = $_POST["search"];

    }

    if($search) {

        echo "<script>window.location.href='result.php?search=$search';</script>";

    }

}


?>


<div class="search-container">
    <h2>Search</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Enter search term">
        <br>
        <span class="error"><?php echo $searchErr; ?></span>
        <br>
        <input type="submit" value="Search">
    </form>
</div>


<style>

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .search-container {
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

    input[type="text"] {
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