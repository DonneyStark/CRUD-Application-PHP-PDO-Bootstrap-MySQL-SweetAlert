<?php
session_start();
if (isset($_POST['submit'])) {
    require 'connect.php';
    $name = $_POST['name'];
    $price = $_POST['price'];
    $detail = $_POST['detail'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, 'uploads/' . $image);
    $stmt = $conn->prepare("INSERT INTO products(name,price,image,detail) 
    VALUES(?,?,?,?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $price);
    $stmt->bindParam(3, $image);
    $stmt->bindParam(4, $detail);
    $result = $stmt->execute();
    if ($result) {
        $_SESSION['success'] = "Succesfully!!!";
        echo "<script> window.location = 'index.php' </script>";
        exit();
    } else {
        $_SESSION['success'] = "Unsuccesfully!!!";
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/4b7aac98e3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-body-tertiary">
    <?php include 'navbar.php'; ?>
    <?php
    if (isset($_GET['id'])) {
        include 'connect.php';
        $id = $_GET['id'];
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <div class="container">
        <div class="row mt-1 g-4">
            <div class="col-sm-8">
                <h4>Home-Manage Product</h4>
                <?php if(empty($row['id'])){ ?>
                    <form action="index.php" method="post" enctype="multipart/form-data">
                <?php }else{ ?>
                    <form action="edit.php?id=<?php echo $row['id']; ?>" method="post" enctype="multipart/form-data">
                <?php } ?>
                
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="">Product Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="">Price</label>
                            <input type="text" name="price" class="form-control" value="<?php echo $row['price']; ?>">
                        </div>
                        <div class="col-sm-6">
                            <div><img src="uploads/<?php echo $row['image']; ?>" alt="" width="100"></div>
                            <label for="">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-sm-12">
                            <label for="">Detail</label>
                            <textarea name="detail" rows="4" class="form-control"><?php echo $row['detail']; ?></textarea>
                        </div>

                    </div>
                    <?php if(empty($_GET['id'])){ ?>
                        <button type="submit" class="btn btn-primary mt-3" name="submit"><i class="fa-regular fa-floppy-disk me-1"></i>Create</button>
                    <?php }else{ ?>
                        <button type="submit" class="btn btn-primary mt-3" name="submit"><i class="fa-regular fa-floppy-disk me-1"></i>Edit</button>
                    <?php } ?>
                        <a href="index.php" class="btn btn-secondary mt-3"><i class="fa-solid fa-rotate-left me-1"></i>Cancel</a>
                </form>
                <?php
                if (isset($_SESSION['success'])) { ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: '<?php echo $_SESSION['success']; ?>'
                        })
                    </script>

                <?php
                    unset($_SESSION['success']);
                } ?>
                <?php if (isset($_SESSION['delete'])) { ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ',
                            text: '<?php echo $_SESSION['delete']; ?>'
                        })
                    </script>
                <?php
                    unset($_SESSION['delete']);
                } ?>
                <?php if (isset($_SESSION['update'])) { ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'แก้ไขสำเร็จ',
                            text: '<?php echo $_SESSION['update']; ?>'
                        })
                    </script>
                <?php
                    unset($_SESSION['update']);
                } ?>
                <hr>
            </div>
        </div>

        <table class="table table-bordered border-info">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connect.php';
                $stmt = $conn->prepare("SELECT * FROM products");
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $row) { ?>


                    <tr>
                        <td><img src="uploads/<?php echo $row['image']; ?>" width="100"></td>
                        <td>
                            <?php echo $row['name']; ?>
                            <?php echo "<div><small class = 'text-secondary'>" . $row['detail'] . "</small></div>"; ?>
                        </td>
                        <td><?php echo $row['price']; ?></td>
                        <td>
                            <a href="index.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-dark"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</a></button>
                            <a href="#" class="btn btn-outline-danger" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fa-solid fa-trash me-1"></i>Delete</a>

                            <!-- <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are You Sure?')" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</a></button> -->
                        </td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>

    </div>
    <script>
    function confirmDelete(productId) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก' 
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete.php?id=' + productId;
            }
        });
    }
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>