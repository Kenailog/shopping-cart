<?php
session_start();
include_once("db_connection.php");

array_walk($_POST, function (&$value, $key) {
    $value = $GLOBALS['conn']->real_escape_string($value);
});

array_walk($_GET, function (&$value, $key) {
    $value = $GLOBALS['conn']->real_escape_string($value);
});

if (!isset($_SESSION['koszyk'])) {
    $_SESSION['koszyk'] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['usun'])) {
        $conn->query("DELETE FROM towary WHERE identyfikator=" . $_GET['usun'] . "") or die($conn->error);
        echo "Usunieto " . $conn->affected_rows;
    }

    if (isset($_GET['edytuj'])) {
        $edit = $conn->query("SELECT * FROM towary WHERE identyfikator=" . $_GET['edytuj'] . "") or die($conn->error);
        $row = $edit->fetch_array();
        ?>
        <form action="sklep.php" method="post">
            <input type="hidden" name="id" value="<?= $_GET["edytuj"] ?>">
            <p>
                <label for="nazwa">Nazwa</label>
                <input type="text" name="nazwa" value="<?= $row["nazwa"] ?> " required>
            </p>
            <p>
                <label for="kategoria">Kategoria</label>
                <input type="text" name="kategoria" value="<?= $row["kategoria"] ?>" required>
            </p>
            <p>
                <label for="ilosc">Ilosc</label>
                <input type="text" name="ilosc" value="<?= $row["ilosc"] ?>" required>
            </p>
            <p>
                <label for="cena">Cena</label>
                <input type="text" name="cena" value="<?= $row["cena"] ?>" required>
            </p>
            <p>
                <button type="submit" name="edytuj">Zapisz zmiany</button>
            </p>
            <?php
            return;
        }

        if (isset($_GET['dodaj'])) {
            ?>
            <form action="sklep.php" method="post">
                <p>
                    <label for="nazwa">Nazwa</label>
                    <input type="text" name="nazwa" id="nazwa" required>
                </p>
                <p>
                    <label for="kategoria">Kategoria</label>
                    <input type="text" name="kategoria" id="kategoria" required>
                </p>
                <p>
                    <label for="ilosc">Ilosc</label>
                    <input type="text" name="ilosc" id="ilosc" required>
                </p>
                <p>
                    <label for="cena">Cena</label>
                    <input type="text" name="cena" id="cena" required>
                </p>
                <p>
                    <button type="submit" name="dodaj">Zapisz zmiany</button>
                </p>
                <p><a href='sklep.php'>Powrot</p>
                <?php
                return;
            }

            if (isset($_GET['szukaj'])) {
                ?>
                <form action="sklep.php" method="post">
                    <p>
                        <label for="nazwa">Nazwa</label>
                        <input type="text" name="nazwa" id="nazwa">
                    </p>
                    <p>
                        <label for="kategoria">Kategoria</label>
                        <input type="text" name="kategoria" id="kategoria">
                    </p>
                    <p>
                        <button type="submit" name="szukaj">Szukaj</button>
                    </p>
                    <p><a href='sklep.php'>Powrot</p>
                    <?php
                    return;
                }
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['edytuj'])) {
                    $query = "UPDATE towary SET nazwa = '" . $_POST["nazwa"] . "', 
              kategoria = '" . $_POST["kategoria"] . "',
              ilosc = " . $_POST["ilosc"] . ",
              cena = " . $_POST["cena"] . " WHERE identyfikator = " . $_POST["id"] . ";";
                    $conn->query($query) or die($conn->error);
                }

                if (isset($_POST['dodaj'])) {
                    $query = "INSERT INTO towary VALUES('', '" . $_POST['nazwa'] . "',
              '" . $_POST['kategoria'] . "', '" . $_POST['ilosc'] . "',
              '" . $_POST['cena'] . "')";
                    $conn->query($query) or die($conn->error);
                }

                if (isset($_POST['szukaj'])) {
                    $query = "SELECT nazwa, kategoria FROM towary WHERE nazwa = '" . $_POST['nazwa'] . "' OR kategoria = '" . $_POST['kategoria'] . "'";
                    $result = $conn->query($query) or die($conn->error);
                    if ($result->num_rows == 0) {
                        echo "<p>Nie znaleziono towarow</p>";
                    } else {
                        echo "<table>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row['nazwa'] . "</td>";
                            echo "<td>" . $row['kategoria'] . "</td></tr>";
                        }
                        echo "</table>";
                    }
                    echo "<a href='sklep.php'>Powrot";
                    return;
                }
            }

            $result = $conn->query("SELECT * FROM towary") or die($conn->error);

            echo "<table>";
            echo "<tr><td>ID</td><td>Nazwa</td><td>Kategoria</td><td>Ilosc</td><td>Cena</td></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row['identyfikator'] . "</td>";
                echo "<td>" . $row['nazwa'] . "</td>";
                echo "<td>" . $row['kategoria'] . "</td>";
                echo "<td>" . $row['ilosc'] . "</td>";
                echo "<td>" . $row['cena'] . "</td><td><a href='sklep.php?usun=" . $row['identyfikator'] . "'>Usun</td></td>
        <td><a href='sklep.php?edytuj=" . $row['identyfikator'] . "'>Edytuj</td>
        <td><a href='koszyk.php?id=" . $row['identyfikator'] . "'>Dodaj do koszyka</td></tr>";
            }
            ?>
            </table>
            <p><a href='sklep.php?dodaj'>Dodaj</p>
            <p><a href='sklep.php?szukaj'>Szukaj</p>
            <p><a href='koszyk.php'>Pokaz koszyk</p>