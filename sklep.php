<?php
session_start();
include_once("db_connection.php");
include_once("layout.php");

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
        echo "<div class='alert alert-warning' role='alert'>Usunieto " . $conn->affected_rows . "</div>";
    }

    if (isset($_GET['edytuj'])) {
        $edit = $conn->query("SELECT * FROM towary WHERE identyfikator=" . $_GET['edytuj'] . "") or die($conn->error);
        $row = $edit->fetch_array();
        ?>
        <form action="sklep.php" method="post">
            <div class="form-group">
                <input type="hidden" name="id" value="<?= $_GET["edytuj"] ?>">
                <label for="nazwa">Nazwa</label>
                <input type="text" class="form-control" name="nazwa" value="<?= $row["nazwa"] ?> " required>
            </div>
            <div class="form-group">
                <label for="kategoria">Kategoria</label>
                <input type="text" class="form-control" name="kategoria" value="<?= $row["kategoria"] ?>" required>
            </div>
            <div class="form-group">
                <label for="ilosc">Ilosc</label>
                <input type="text" class="form-control" name="ilosc" value="<?= $row["ilosc"] ?>" required>
            </div>
            <div class="form-group">
                <label for="cena">Cena</label>
                <input type="text" class="form-control" name="cena" value="<?= $row["cena"] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="edytuj">Zapisz zmiany</button>
        </form>
        <?php
        return;
    }

    if (isset($_GET['dodaj'])) {
        ?>
        <form action="sklep.php" method="post">
            <div class="form-group">
                <label for="nazwa">Nazwa</label>
                <input type="text" class="form-control" name="nazwa" id="nazwa" required>
            </div>
            <div class="form-group">
                <label for="kategoria">Kategoria</label>
                <input type="text" class="form-control" name="kategoria" id="kategoria" required>
            </div>
            <div class="form-group">
                <label for="ilosc">Ilosc</label>
                <input type="text" class="form-control" name="ilosc" id="ilosc" required>
            </div>
            <div class="form-group">
                <label for="cena">Cena</label>
                <input type="text" class="form-control" name="cena" id="cena" required>
            </div>
            <button type="submit" class="btn btn-primary" name="dodaj">Zapisz zmiany</button>
        </form>
        <p><a href='sklep.php'>Powrot</p>
        <?php
        return;
    }

    if (isset($_GET['szukaj'])) {
        ?>
        <form action="sklep.php" method="post">
            <div class="form-group">
                <label for="nazwa">Nazwa</label>
                <input type="text" class="form-control" name="nazwa" id="nazwa">
            </div>
            <div class="form-group">
                <label for="kategoria">Kategoria</label>
                <input type="text" class="form-control" name="kategoria" id="kategoria">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="szukaj">Szukaj</button>
            </div>
            <p><a href='sklep.php'>Powrot</p>
        </form>
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
            echo "<div class='alert alert-warning' role='alert'>Nie znaleziono towarow</div>";
        } else {
            echo "<table class='table'><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><th scope='row'>" . $row['nazwa'] . "</th>";
                echo "<td>" . $row['kategoria'] . "</td></tr>";
            }
            echo "</tbody></table>";
        }
        echo "<a href='sklep.php'>Powrot";
        return;
    }
}

$result = $conn->query("SELECT * FROM towary") or die($conn->error);

echo "<table class='table'>";
echo "<thead class='thead-dark'><th scope='col'>ID</th><th scope='col'>Nazwa</th><th scope='col'>Kategoria</th><th scope='col'>Ilosc</th><th scope='col'>Cena</th></tr><thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><th scope='row'>" . $row['identyfikator'] . "</th>";
    echo "<td>" . $row['nazwa'] . "</td>";
    echo "<td>" . $row['kategoria'] . "</td>";
    echo "<td>" . $row['ilosc'] . "</td>";
    echo "<td>" . $row['cena'] . "</td><td><a href='sklep.php?usun=" . $row['identyfikator'] . "'>Usun</td></td>
        <td><a href='sklep.php?edytuj=" . $row['identyfikator'] . "'>Edytuj</td>
        <td><a href='koszyk.php?id=" . $row['identyfikator'] . "'>Dodaj do koszyka</td></tr>";
}
echo "</tbody>";
echo "</table>";

?>
<p><a href='sklep.php?dodaj'>Dodaj</p>
<p><a href='sklep.php?szukaj'>Szukaj</p>
<p><a href='koszyk.php'>Pokaz koszyk</p>