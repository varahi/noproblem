var userInfo = {
    username: new Date().getTime().toString()
};
/* var form = document.getElementById('submit-form');
var messageArea = document.getElementById("form-message");
var messagesList = document.getElementById('messages-list');
 */

// Socket chat configuration
/**
 * @type WebSocket
 */

var socket = new WebSocket("ws://127.0.0.1:8080");

socket.onopen = function(event) {
    console.info("Connection status OK!");
};

socket.onmessage = function(event) {
    var data = JSON.parse(event.data);
    ChatController.addMessage(data.username, data.message);

    console.log(data);
};

socket.onerror = function(error){
    alert("Something is wrong with your connection.");
    console.error(error);
};
// Socket chat config end


/// Adding messages to the list element
document.getElementById("form-submit").addEventListener("click",function(){
    var msg = document.getElementById("form-message").value;

    if(!msg){
        alert("You haven't written anything yet!");
    }

    ChatController.sendMessage(msg);
    // Clear out the text field.
    document.getElementById("form-message").value = "";
}, false);


// Send the message and add it to a list.
var ChatController = {
    addMessage: function(username,message){
        var from;

        if(username == userInfo.username){
            from = "Me";
        }else{
            from = userInfo.username;
        }

        var ul = document.getElementById("messages-list");
        var li = document.createElement("li");
        li.appendChild(document.createTextNode(from + " : "+ message));
        ul.appendChild(li);
    },
    sendMessage: function(text){
        userInfo.message = text;
        socket.send(JSON.stringify(userInfo));
        this.addMessage(userInfo.username, userInfo.message);
    }
};