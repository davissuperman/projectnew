/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function() {
    var serverUrl, socket, statusMsg;
    log = function(msg) {
        return $('#log').prepend("" + msg + "<br />");
    };
    serverUrl = 'ws://127.0.0.1:8080/index.php?g=customer&m=Main&a=index';
    if (window.MozWebSocket) {      
        socket = new MozWebSocket(serverUrl);
    } else if (window.WebSocket) {
        socket = new WebSocket(serverUrl);
    }     
    socket.onopen = function(msg) {
        alert(this.readyState);
        return $('#status').removeClass().addClass('online').html('connected');
    };
    socket.onmessage = function(msg) {
        var response;
        response = JSON.parse(msg.data);
        switch (response.action) {
            case "statusMsg":
                return statusMsg(response.data);
            case "clientConnected":
                return clientConnected(response.data);
            case "clientDisconnected":
                return clientDisconnected(response.data);
            case "clientActivity":
                return clientActivity(response.data);
            case "serverInfo":
                return refreshServerinfo(response.data);
        }
    };
    socket.onclose = function(msg) {
        return $('#status').removeClass().addClass('offline').html('disconnected');
    };
    $('#status').click(function() {
        return socket.close();
    });
}).call(this);