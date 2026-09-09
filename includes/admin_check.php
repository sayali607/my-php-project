
<?php 

session_start(); 

if ( 
    !isset($_SESSION["admin_id"]) || 
    empty($_SESSION["admin_id"]) 
) { 

    header("Location: ../admin/login.php"); 

    exit; 
} 

if ( 
    !isset($_SESSION["admin_role"]) || 
    $_SESSION["admin_role"] !== "admin" 
) { 

    $_SESSION = []; 

    if (ini_get("session.use_cookies")) { 

        $params = session_get_cookie_params(); 

        setcookie( 
            session_name(), 
            "", 
            time() - 42000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"] 
        ); 
    } 

    session_destroy(); 

    header("Location: ../admin/login.php"); 

    exit; 
} 

?>
