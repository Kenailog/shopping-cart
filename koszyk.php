<?php
include_once("db_connection.php");
session_start();

if (!isset($_SESSION['koszyk'])) {
    $_SESSION['koszyk'] = array();
}

if (isset($_GET['id'])) {
    $_SESSION['koszyk'][$_GET['id']] == 0 ? $_SESSION['koszyk'][$_GET['id']] = 1 : $_SESSION['koszyk'][$_GET['id']]++;
}

if (empty($_SESSION['koszyk'])) {
    echo "<p>Brak towarow w koszyku</p>";
    echo "<a href='sklep.php'>Wroc do sklepu";
    return;
}

if (isset($_POST['zmien'])) {
    array_walk($_POST['amount'], function (&$element, $key) {
        $element = strip_tags($element);
    });

    foreach ($_POST['id'] as $key => $value) {
        $_SESSION['koszyk'][$value] = $_POST['amount'][$key];
    }
}

$query = "SELECT * FROM towary WHERE identyfikator IN(";

foreach ($_SESSION['koszyk'] as $key => $value) {
    $key = $conn->real_escape_string($key);
    $query .= "$key,";
}

$query[strlen($query) - 1] = ")";
$result = $conn->query($query) or die($conn->error);

echo "<table>";
echo "<tr><td>Nazwa</td><td>Kategoria</td><td>Ilosc</td><td>Cena</td><td>Zmien ilosc</td></tr>";
while ($row = $result->fetch_assoc()) {
    $amount = $_SESSION['koszyk'][$row['identyfikator']];
    $price = $amount * $row['cena'];
    $total_cost += $price;
    echo "<tr><td>" . $row['nazwa'] . "</td>";
    echo "<td>" . $row['kategoria'] . "</td>";
    echo "<td>" . $amount . "</td>";
    echo "<td>" . $price . "</td><td><form action='koszyk.php' method='post'>
                               <input type='text' name='amount[]' value=" . $amount . ">
                               <input type='hidden' name='id[]' value=" . $row['identyfikator'] . ">
                               <button type='submit' name='zmien'>Zmien</button></td></tr>";
}
echo "</table>";

foreach ($_SESSION['koszyk'] as $value) {
    $number += $value;
}

echo "<p>Ilosc towarow: $number</p>";
echo "<p>Cena: $total_cost</p>";
?>
<a href="sklep.php">Wroc do sklepu