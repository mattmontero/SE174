
<?php
    tests();
    require_once "validate.html";
    $username = $email = $password = "";
    if(isset($_POST['username']))
        $username = fix_string($_POST["username"]);
    if(isset($_POST['email']))
        $email = fix_string($_POST["email"]);
    if(isset($_POST['password']))
        $password = fix_string($_POST["password"]);
    
    $fail = validate_username($username);
    $fail .= validate_email($email);
    $fail .= validate_password($password);
    if($fail == ""){
        echo "</head><body>User input verified</body></html>";
        exit;
    }

    function tests(){
        echo "Actual : ".validate_username("");
        echo " -> Expected : No username was entered.<br>";
        echo "Actual : ".validate_username("Abcd");
        echo " -> Expected : Username must be at least 5 characters long.<br>";
        echo "Actual : ".validate_username("Abcde");
        echo " -> Expected : ''<br>";
        echo "Actual : ".validate_username("Abcde%");
        echo " -> Expected : Only english letters, digits, underscores, and dashs are valid input.<br>";
        echo "Actual : ".validate_email("");
        echo " -> Expected : No email was entered.<br>";
        echo "Actual : ".validate_email("Angel@com.google");
        echo " -> Expected : ''<br>";
        echo "Actual : ".validate_email("Bird@canvas");
        echo " -> Expected : The email address is not valid.<br>";
        echo "Actual : ".validate_email("Snacks");
        echo " -> Expected : The email address is not valid.<br>";
        echo "Actual : ".validate_password("");
        echo " -> Expected : No password was entered.<br>";
        echo "Actual : ".validate_password("Abcd");
        echo " -> Expected : Passwords must be at least 5 characters long!<br>";
        echo "Actual : ".validate_password("abc123");
        echo " -> Expected : Passwords must contain at least:\n\t1 lowercase letter.\n\t1 uppercase letter.\n\t1 number.<br>";
        echo "Actual : ".validate_password("ABC123");
        echo " -> Expected : Passwords must contain at least:\n\t1 lowercase letter.\n\t1 uppercase letter.\n\t1 number.<br>";
        echo "Actual : ".validate_password("Abc123");    
        echo " -> Expected : ''<br>";    
    }

    function fix_string($str){
        if(get_magic_quotes_gpc())
            $str = stripcslashes($str);
        return htmlentities($str);
    }
    
    function validate_email($email){
        if($email == "")
            return "No email was entered<br>";
        else if(!((strpos($email, ".") > 0) && (strpos($email, "@") > 0)) || preg_match("/[^a-zA-Z0-9.@_-]/", $email))
            return "The email address is not valid.<br>";
        return "";
    }

    function validate_username($username){
        if($username == "")
            return "No username was entered<br>";
        else if(strlen($username) < 5)
            return "Usernames must be at least 5 characters long<br>";
        else if(preg_match("/[^a-zA-Z0-9_-]/", $username))
            return "Only english letters, digits, underscores, and dashs are valid input.<br>";
        return "";
    }
    
    function validate_password($password){
        if($password == "")
            return "No password was entered<br>";
        else if(strlen($password) < 5)
            return "Passwords must be at least 5 characters long!<br>";
        else if(!preg_match("/[a-z]/", $password) ||
                !preg_match("/[A-Z]/", $password) ||
                !preg_match("/[0-9]/", $password))
            return "Passwords should contain at least 1:\n\tLowercase Letter\n\tUppercase Letter\n\tDigit.<br>";
        return "";
    }
?>
