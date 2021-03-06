<?php
include "../includes/RequestBook.php";
include "../includes/Config_inc.php";
include "../includes/Users.php";
include "../includes/Books.php";

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
$error = "";

if (isset($_GET['id_book'])) {
    $id_book = $_GET['id_book'];
    $status = $books->getStatus($db, $id_book);

    if ($status != 1) {

        $request = new RequestBook();

        if (!$request->isRequest($db, $user->getId(), $id_book)) {
            echo json_encode(["status" => "0"]);
        }

        } else
            echo json_encode(["status" => "1"]);
    }
