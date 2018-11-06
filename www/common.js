var indexFile = "index.php"; 

function request(params, callback) {
	var http = new XMLHttpRequest();
	http.open('POST', indexFile, true);
	http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	http.onreadystatechange = callback;
	http.send(params)
}

function logout() {
	request('action=logout', function() {
		if(this.readyState == 4 && this.status == 200) {
			location.reload();
		}
	});
}

function login(user, pass, callback){
	request('action=login&user='+encodeURIComponent(user)+'&pass='+encodeURIComponent(pass), function() {
			if(this.readyState == 4 && this.status == 200) {
				callback(true, this.responseText);
			}
		});
}
function getvalueat(sensor, time, callback) {
	request('action=getvalueat&sensor=' + sensor + '&time=' + time, function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getvalue(sensor, callback) {
	request('action=getvalue&sensor=' + sensor, function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getusername(callback) {
	request('action=getusername', function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getusertype(callback) {
	request('action=getusertype', function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getusers(callback) {
	request('action=getusers', function() {
		if(this.readyState == 4 && this.status == 200) {
			var text = JSON.parse(this.responseText);

			if(text)
				callback(true, text);
		}
	});
}

function setrole(callback, id, role) {
	request('action=setrole&id='+encodeURIComponent(id)+'&role='+encodeURIComponent(role), function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function changepass(callback, oldpass, newpass) {
	request('action=changepass&oldpass='+encodeURIComponent(oldpass)+'&newpass='+encodeURIComponent(newpass), function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getsensorlist(callback) {
	request('action=getsensorlist', function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, JSON.parse(this.responseText));
		}
	});
}

function getconfig(device, name, callback) {
	request('action=getconfig&device=' + device + '&name=' + name, function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, this.responseText);
		}
	});
}

function getlastvalue(device, callback) {
	request('action=getlastvalue&sensor=' + encodeURIComponent(device), function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, JSON.parse(this.responseText));
		}
	});
}

function getsensorlistname(callback) {
	request('action=getsensorlistname', function() {
		if(this.readyState == 4 && this.status == 200) {
			callback(true, JSON.parse(this.responseText));
		}
	});
}

function getallconfig(device, callback) {
	request('action=getallconfig&device=' + device, function() {
		if(this.readyState == 4 && this.status == 200) {
			var text = JSON.parse(this.responseText);

			if(text)
				callback(true, text, device);
		}
	});
}

function setconfig(device, name, value, callback) {
	request('action=setconfig&device='+encodeURIComponent(device)+'&name='+encodeURIComponent(name)+'&value='+encodeURIComponent(value),
		function() {
			if(this.readyState == 4 && this.status == 200) {
				callback(true, this.responseText, device, name, value);
			}
		});
}

//getgraphdata
function getgraphdata(device, from, to, callback) {
	request('action=getgraphdata&device='+encodeURIComponent(device)+'&from-date='+encodeURIComponent(from)+'&to-date='+encodeURIComponent(to),
		function() {
			if(this.readyState == 4 && this.status == 200) {
				var text = JSON.parse(this.responseText);
				
				if(text)
					callback(true, text, device);
			}
		});
}