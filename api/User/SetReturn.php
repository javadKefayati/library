<?php

include "../includes/RequestBook.php";
include "../includes/Config_inc.php";
include "../includes/Users.php";
include "../includes/Books.php";
include "../includes/Admin.php";

$db = new Config_inc("library2");
$user = new Users();
$books = new Books();

try {
    $check = $user->isLogIn($db);
    if ($check == 0)
        header('Location:../Sign/SignIn.php');
    elseif ($check == 2)
        header('Location:../admin/LibraryAdmin.php');

} catch (Exception $e) {

}

$req = new RequestBook();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($req->checkReturn($db, $id)) {
        $req->setReturned($db, $id);
        echo json_encode(["status" => "1"]);
    } else
        echo json_encode(["status" => "0"]);
}
