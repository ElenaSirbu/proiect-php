<?php
include('db_config.php');

$query = "SELECT * FROM produse";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Produse disponibile</title>
</head>
<body>
    <h1>Produse disponibile</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Preț</th>
                <th>Descriere</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><a href="order_product.php?id=<?php echo $row['id']; ?>">Comandă</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
