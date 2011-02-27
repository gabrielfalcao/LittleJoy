class DataJoy {
    public static function MySQL($host, $user, $pass, $db){
        $con = mysql_connect($host, $user, is_null($pass) ? "": $pass);
        mysql_select_db($db, $con);
    }
}
