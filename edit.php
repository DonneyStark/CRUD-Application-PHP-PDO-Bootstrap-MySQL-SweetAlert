<?php
session_start();
if(isset($_GET['id'])){
    include 'connect.php';
    $id = $_GET['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    if(empty($image)){
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $image = $row['image'];

    }
    $detail = $_POST['detail'];

    move_uploaded_file($tmp, 'uploads/' . $image);
    $sql = "UPDATE products SET name = ?, price = ?, image = ?, detail = ?
    WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1,$name);
    $stmt->bindParam(2,$price);
    $stmt->bindParam(3,$image);
    $stmt->bindParam(4,$detail);
    $stmt->bindParam(5,$id);
    $result = $stmt->execute();
    
    if($result){
        $_SESSION['update'] = 'Update Succesfully';
        header('location: index.php');
        exit();
    }else{
        $_SESSION['update'] = 'Cannot Update';
        header('location: index.php');
        exit();
    }
    
}
?>