<?php
session_start();
if (isset($_GET['id'])) {
    include 'connect.php';
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $id);
    $result = $stmt->execute();

    if($result){
        $_SESSION['delete'] = 'Delete Succesfully';
        header('location: index.php');
        exit();
    }else{
        $_SESSION['delete'] = 'Cannot delete';
        header('location: index.php');
        exit();
    }
}
