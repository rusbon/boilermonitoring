
var mysql = require("mysql")
var firebase = require("firebase");
const WebSocket = require('ws');

// State Variable
let level;
let pump;
let dataPumpRetrieved = false;
let dataLevelRetrieved = false;

// MySQL Config
var db = mysql.createConnection({
  host: "localhost",
  user: "casio",
  password: "Sapiompong.53",
  database: "plcmonitoring"
});

// Connecting MySQL
db.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
});

// Websocket Server
const wss = new WebSocket.Server({ port: 8088 });

// Firebase Config
const firebaseConfig = {
  apiKey: "AIzaSyDOmQ08XptkM3NlH8k_3o9P7pjHLAUPmEE",
  authDomain: "finalprojectar-28391.firebaseapp.com",
  databaseURL: "https://finalprojectar-28391.firebaseio.com",
  projectId: "finalprojectar-28391",
  storageBucket: "finalprojectar-28391.appspot.com",
  messagingSenderId: "534910582402",
  appId: "1:534910582402:web:f7ca4af451cf7e8cd33e9b",
  measurementId: "G-3NRBT7PZTR"
};

// Firebase Initiation
firebase.initializeApp(firebaseConfig);

// Get a reference to the database service
var database = firebase.database();

var fLevel = database.ref('Sensor/Level');
var fPump = database.ref('Output/Pump');



// set Event Listener on data Change
fPump.on('value', (snapshot) =>{
  pump = snapshot.val();
  console.log(pump);
  if(dataLevelRetrieved){
    storeSQL();
  }

  dataPumpRetrieved = true;
});

fLevel.on('value', (snapshot) =>{
  level = snapshot.val();
  console.log(level);
  if(dataPumpRetrieved){
    storeSQL();
  }

  dataLevelRetrieved = true;
});

// Logging Data every 1 minute
let eventTimer = function(){
  fPump.once('value').then((snapshot) => {
    pump = snapshot.val();
  });
  fLevel.once('value').then((snapshot) => {
    level = snapshot.val();
  });

  if(dataPumpRetrieved && dataLevelRetrieved){
    storeSQL();
  }

  // Sending data websocket
  dataWs = {
    'timestamp': Date.now(),
    'level': level,
    'pump': pump
  }

  wss.clients.forEach(function each(client) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(dataWs));
    }
  });
}

// SQL Query Inserting data to database
var storeSQL = function(){
  var sql = "INSERT INTO `log` (`level`,  `pump_control`, `date`, `time`) VALUES ("+ level +", '"+ pump +"', NOW(), NOW())";
  db.query(sql, function (err, result){
    if (err) throw err;
    console.log('data inserted');
  })
}

// // SQL QueryG
// var getLastSQL = function(){
//   var sql = "INSERT INTO `log` (`level`,  `pump_control`, `date`, `time`) VALUES ("+ level +", '"+ pump +"', NOW(), NOW())";
//   db.query(sql, function (err, result){
//     if (err) throw err;
//     console.log(Date.now());
//   })
// }

setInterval(eventTimer, 60000);


// let test = function(){
//   wss.clients.forEach(function each(client) {
//     if (client.readyState === WebSocket.OPEN) {
//       client.send(Date.now());
//     }
//   });
// }
// setInterval(test, 5000);