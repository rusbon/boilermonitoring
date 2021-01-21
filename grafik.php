<?php
include 'database-config.php';

$sql = "SELECT * FROM (SELECT `id`, `level`, `pump_control`, `date`, `time` FROM `log` ORDER BY `id` DESC limit 100)Var1 ORDER BY `id` ASC";
$result = $conn->query($sql);

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

  <!-- Script -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
      <li><a href="grafik.php" class="active">Grafik</a></li>
      <li><a href="histori.php">Histori</a></li>
    </ul>
  </nav>

  <main>
    <div class="logo-institusi">
      <img src="img/logo-its.png" alt="">
      <img src="img/logo-tf.png" alt="">
      <img src="img/logo-ecs.png" alt="">
    </div>

    <div id="chart" class="card">
      <div id="errorMessage" class="error">
        Connection Problem! ,Reconnecting.....
      </div>
      <h2>Grafik Data Level Terkini</h2>
      <div>
        <div id="linechart_material"></div>
      </div>
    </div>
  </main>

  <!-- Script -->
  <!-- Sidebar Button -->
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
  </script>

  <!-- Graph Script -->
  <script type="text/javascript">
    let data;
    let options;

    let chart;

    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      data = google.visualization.arrayToDataTable([
        ['Time', 'Level'],
// Adding Data from database to Graph
<?php
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'] . "T" . $row['time'];
        // echo $row['level'] . "|" . $row['pump_control'] . "|" . $row['time'] . "<br>";
        echo "[new Date('" . $date . "'), " . $row['level'] . "], \n";
    }
}
?>
      ]);

      options = {
        title: 'Level Air',
        legend: {
          position: 'bottom'
        },
        vAxis:{
          ticks:[
            {v:0, f:'LOW LOW'},
            {v:1, f:'LOW'},
            {v:2, f:'HIGH'},
            {v:3, f:'HIGH HIGH'}
          ]
        }
      };

      chart = new google.visualization.LineChart(document.getElementById('linechart_material'));

      chart.draw(data, options);

    }

    var addGraphData = function(e){
      let dataWs = JSON.parse(e);
      data.removeRow(0);
      data.addRows([[new Date(dataWs.timestamp), dataWs.level]]);
      chart.draw(data, options);
    }
  </script>

  <script>
    function socketConnect(){
      // Create WebSocket connection.
      const socket = new WebSocket('ws://localhost:8088');
      console.log('connected');

      socket.onclose = function(e) {
        console.log('Socket is closed. Reconnect will be attempted in 2 second.', e.reason);
        setTimeout(function() {
          socketConnect();
        }, 2000);
      };
      // Listen for messages
      socket.addEventListener('message', function (event) {
        console.log('Message from server ', event.data);
        addGraphData(event.data);
      });
    }

    socketConnect();
  </script>
  <!-- Firebase Script -->
  <!-- <script>
    //Firebase Config
    const firebaseConfig = {
      apiKey: "AIzaSyDOmQ08XptkM3NlH8k_3o9P7pjHLAUPmEE",
      authDomain: "finalprojectar-28391.firebaseapp.com",
      databaseURL: "https://finalprojectar-28391.firebaseio.com",
      projectId: "finalprojectar-28391",
      storageBucket: "finalprojectar-28391.appspot.com",
      messagingSenderId: "534910582402",
      appId: "1:534910582402:web:6069fe7b7dcb0b9bd33e9b",
      measurementId: "G-PWYP7ZSWT1"
    };

    // Firebase Initiation
    firebase.initializeApp(firebaseConfig);

    // Get a reference to the database service
    let database = firebase.database();
    let fLevel = database.ref('Sensor/Level');
    let fPump = database.ref('Output/Pump');

    // set Event Listener on data Change
    fLevel.on('value', (snapshot) => {
      addGraphData(snapshot.val());
    });

    // fPump.on('value', (snapshot) => {
    //   setPump(snapshot.val());
    // });
  </script> -->

</body>

</html>
