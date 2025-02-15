<?php
include 'db_config.php';

$result = $conn->query("SELECT Products.id, Products.name, Products.price, Products.quantity, Categories.name AS category 
                        FROM Products 
                        JOIN Categories ON Products.category_id = Categories.id");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<h2>Lista Produse</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nume</th>
        <th>Preț</th>
        <th>Cantitate</th>
        <th>Categorie</th>
        <th>Acțiuni</th>
    </tr>
    <?php foreach ($products as $product) { ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td><?= $product['price'] ?> RON</td>
            <td><?= $product['quantity'] ?></td>
            <td><?= $product['category'] ?></td>
            <td>
                <a href="update_product.php?id=<?= $product['id'] ?>">Editează</a> | 
                <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Sigur ștergi produsul?')">Șterge</a>
            </td>
        </tr>
    <?php } ?>
</table>
