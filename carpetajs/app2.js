// imports
const express = require('express');
const app = express();
const bodyParser = require('body-parser');


// inicializamos la conexion con firebase
// necesitamos json con las credenciales 
var admin = require('firebase-admin');
var serviceAccount = require('./dbfirebase.json');
admin.initializeApp({

    credential: admin.credential.cert(serviceAccount),
    databaseURL: 'https://pushnotification-5c828.firebaseio.com'
});

var db = admin.database();
var ref = db.ref("/partidas");

// cuando hay un cambio en la base de datos enviaremos la notificacion a los dos jugadores, porrque solo le llegara al que no este dentro de ella 
ref.on("child_changed", function(snapshot) {
   var resultado = null;
    var propiedad = null;
    var jugador1=null;
    var jugador2=null;
    var tokenJugador1=null;
    var tokenjugador2=null;
    
    console.log("dentro de la funcion:" + snapshot.val());
    resultado = snapshot.val();
    // recogemos el id de la partida donde hubo una modificacion 
    var partida=snapshot.key;
   
    // recogemos el nick del jugador para mandarle la notificacion 
    var ref2=db.ref("/partidas/"+partida+"/nick1");
    ref2.on('value',function(snapshot){
         jugador1=snapshot.val();
 
    });
    var ref4=db.ref("/partidas/"+partida+"/nick2");
    ref4.on('value',function(snapshot){
         jugador2=snapshot.val();
 
    });
    
    
    // recogemos el token de los jugadores de la entrada en la base de datos donde tenemos a todos almacenados 
    
    var ref3=db.ref("/jugadores/"+jugador1+"/token");
    ref3.on('value',function(snapshot){
          tokenJugador1 =snapshot.val();

     var ref5=db.ref("/jugadores/"+jugador2+"/token");
    ref3.on('value',function(snapshot){
          tokenjugador2 =snapshot.val();
    
    var token =tokenJugador1;
    var token2= tokenjugador2;
    

    let msg = "Tu oponente te ha preguntado, responde o se enfadara";
  
    
    var registrationToken = token;
    var registrationToken2 = token2;

    // Creamos el cuerpo de la notificación
    var message = {
        data:{
            msg:msg
        },
        notification:{
            "title":"Es tu turno",
            "body": msg
        },
        token: registrationToken
    };

    //Envío de la notificación
    admin.messaging().send(message)
        .then((response) => {
        // Response is a message ID string.
        console.log('Successfully sent message:', response);
    })
    .catch((error) => {
        console.log('Error sending message:', error);
    });
    
     // enviamos una notificacion tambien al otro jugador
    var message2 = {
        data:{
            msg:msg
        },
        notification:{
            "title":"Es tu turno",
            "body": msg
        },
        token: registrationToken2
    };

    //Envío de la notificación
    admin.messaging().send(message2)
        .then((response) => {
        // Response is a message ID string.
        console.log('Successfully sent message:', response);
    })
    .catch((error) => {
        console.log('Error sending message:', error);
    });
       }); 
    });
    
}, function(errorObject) {
    console.log("The read failed: " + errorObject.code);
});


var server = app.listen(8080, () => {
    console.log('Servidor web iniciado');
});