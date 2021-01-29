<?php
include 'database-config.php';

$sql = "SELECT * FROM `log` WHERE `date` >= (NOW() - INTERVAL 1 MONTH)";
$result = $conn->query($sql);

function level_int_to_string($level)
{
    switch ($level) {
        case 2:
            return 'ALARM';
        case 3:
            return 'LOW';
        case 4:
            return 'HIGH';
        default:
            return 'UNKNOWN';
    }
}

?>
<!DOCTYPE html>
<html>

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BOILER</title>

  <!-- Style -->
  <link rel="stylesheet" href="css/style.css" type="text/css">
  <link rel="stylesheet" href="css/flexboxgrid.min.css">
  <link rel="stylesheet" href="css/gfonts.css">
  <link rel="stylesheet" type="text/css" href="css/datatables.min.css"/>

  <!-- Script -->
  <script type="text/javascript" src="js/datatables.min.js"></script>
</head>

<body>
  <div class="mobile-header">
    <button id="sidebar-toggler" data-show="0" onclick="sidebarToggle()">
      <img src="img/bars.svg" width="36px" alt="">
    </button>
    <a href="#">
      <img src="img/logo-wana-1.png" alt="logo-wana">
    </a>
  </div>

  <nav id="sidebar" class="sidebar">
    <a class="logo" href="#">
      <img src="img/logo-wana-1.png" alt="logo-wana">
    </a>
    <p>Monitoring Level Air Boiler</p>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="grafik.php">Grafik</a></li>
      <li><a href="histori.php" class="active">Histori</a></li>
    </ul>
  </nav>

  <main>
    <div class="logo-institusi">
      <img src="img/logo-its.png" alt="">
      <img src="img/logo-tf.png" alt="">
      <img src="img/logo-ecs.png" alt="">
    </div>

    <div class="tablehistory">
      <h2>Data Monitoring dalam 1 Bulan</h2>
      <table id="table-history">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Level</th>
            <th>Pump Control</th>
          </tr>
        </thead>
        <tbody>
<!-- Adding Data from database to Graph -->
<?php
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "
        <tr>
            <td>" . $row['date'] . "</td>
            <td>" . $row['time'] . "</td>
            <td>" . level_int_to_string($row['level']) . "</td>
            <td>" . $row['pump_control'] . "</td>
        </tr>
        ";
    }
}
?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Script -->
  <script>
    function sidebarToggle() {
      let button = document.getElementById('sidebar-toggler');
      let sidebar = document.getElementById('sidebar');
      if (button.dataset.show == 1) {
        sidebar.classList.remove('show');
        button.dataset.show = 0;
      } else {
        sidebar.classList.add('show');
        button.dataset.show = 1;
      }
    }

    let table = new DataTable('#table-history');
  </script>

</body>

</html>