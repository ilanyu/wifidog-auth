<?php
error_reporting(0);

function showhtml($html)
{
    $content = file_get_contents("./html/" . $html . ".htm");
    $uri = dirname($_SERVER["SCRIPT_NAME"])=="/"?"/":dirname($_SERVER["SCRIPT_NAME"]) . "/";
    echo str_replace('{$url}',"http://" . $_SERVER["HTTP_HOST"] . $uri,$content);
}

function getPassword()
{
//    return substr(md5(date("Ymd") . "ly"),0,6);
    return "88888888";
}

date_default_timezone_set('PRC');
if (isset($_REQUEST["token"]))
    $token = htmlspecialchars($_REQUEST["token"]);
if (isset($_REQUEST["w_password"]))
    $w_password = htmlspecialchars($_REQUEST["w_password"]);
if (isset($_REQUEST["gw_address"]))
    $gw_address = htmlspecialchars($_REQUEST["gw_address"]);
if (isset($_REQUEST["gw_port"]))
    $gw_port = htmlspecialchars($_REQUEST["gw_port"]);
if (isset($_REQUEST["mac"]))
    $mac = htmlspecialchars($_REQUEST["mac"]);
if (isset($_SERVER["PATH_INFO"]))
{
    $url = explode("/",$_SERVER["PATH_INFO"]);
    $url = $url[1];
}
else
{
    $url = "index";
}

$fp = fopen("./log/" . date("Ymd"),"a");
fwrite($fp,date("Y-m-d H:i s") . " " . $_SERVER["REQUEST_METHOD"] . " " . $_SERVER["HTTP_USER_AGENT"] . " " . $_SERVER["HTTP_X_CLIENT_IP"] . " " . $_SERVER["REQUEST_URI"] . "\n");
fclose($fp);

switch ($url)
{
    case "auth":
        if (isset($token))
        {
            if (file_exists("./token/" . $token))
            {
                echo "Auth: 1";
            }
            else
            {
                echo "Auth: 0";
            }
        }
        else
        {
            echo "Auth: 0";
        }
        break;
    case "login":
        if (isset($w_password)&&isset($gw_address)&&isset($gw_port))
        {
            if ($w_password === getPassword())
            {
                $token = md5(time() . rand(100000,999999));
                if (!file_exists("./token/" . $token))
                {
                    file_put_contents("./token/" . $token,time());
                }
                header("Location: http://" . $gw_address . ":" . $gw_port . "/wifidog/auth?token=" . $token);
                exit();
            }
            header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"] . "/msg/?message=Username%20或%20password%20错误!");
        }
        else
        {
            showhtml("login");
        }
        break;
    case "msg":
        showhtml("msg");
        break;
    case "ping":
        echo "Pong";
        break;
    case "portal":
        showhtml("portal");
        break;
    case "gettodaypassword":
        echo getPassword();
        break;
    default:
        echo "Hello World";
}