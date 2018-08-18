<script>
    var chats = [];
    var latestID = -1;
    var user;

    <?php
    if( isset( $_POST ) ) {
        echo "user = " . json_encode( 
            array(
                'name' => $_POST[name],
                'business' => $_POST[business],
                'phone' => $_POST[phone],
                'id' => $_POST[id]
            ) 
        ) . "
        ";
    } else {
        echo "console.log('no post data');";
    }
    ?>

    function sendMessage() {
        var xhttp = new XMLHttpRequest();
        var element;
        console.log('user', user);
        var data = {
            message: document.querySelector('#message').value,
            user: user,
            date: new Date(),
            isSenderSelf: true,
            status: 0,
            id: undefined
        };

        xhttp.open("POST", "/wp-json/chat/v1/new_message", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                var resData = JSON.parse(xhttp.responseText);
                var serverRes = resData.server;
                var message = resData.message;

                data.id = message.id;

                if (serverRes.response.code === 200) {
                   changeStatus(element, message);
                }
            }
        }
        xhttp.send(JSON.stringify(data));

        element = addMessages(data);
    }

    function addMessages(chats) {
        if (!Array.isArray(chats))
            chats = [chats];

        var newMessageEl;
        var dynamichatEl = document.getElementById('dynamichat');
        var messageTemplate = dynamichatEl.querySelector('#first_msg');
        var messagesSection = dynamichatEl.querySelector('.dynamichat_messages');
        
        for (var chat of chats) {
            var newMessageEl = messageTemplate.cloneNode(true);
            
            newMessageEl.style.display = 'block';
            newMessageEl.removeAttribute('id');
            newMessageEl.style['text-align'] = (chat.isSenderSelf ? "right" : "left");
            
            if (!chat.isSenderSelf) {
                newMessageEl.querySelector('.message_picture').style['display'] = "block";
                newMessageEl.querySelector('.message_picture').setAttribute('src', chat.picture);
            } else {
                newMessageEl.querySelector('.message_body_container').classList.add("offset-3");
                newMessageEl.querySelector('.message_status').style['display'] = "inline";
            }
            newMessageEl.querySelector('.message_text').innerHTML = chat.text;
            newMessageEl.querySelector('.message_date').innerHTML = chat.date.toTimeString().split(' ')[0];
            
            messagesSection.append(newMessageEl);

            changeStatus(newMessageEl, chat);
            console.log(chat.id);
            if(typeof chat.id !== 'undefined')
                latestID = chat.id;

            return newMessageEl;
        }
    }

    function changeStatus(element, chat) {
        var statusEl = element.querySelector('.message_status')
        switch(chat.status) {
            case 0:
                statusEl.innerHTML = '';        
                break;
            case 1:
                statusEl.innerHTML = '<i class="fa fa-check">';        
                break;
            case 2:
                statusEl.innerHTML = '<i class="fa fa-check-double">';        
                break;
        }
    }

    function getChats() {
        var xhttp = new XMLHttpRequest();

        xhttp.open("GET", "/wp-json/chat/v1/messages?latestID="+latestID, true);
        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200) {
                var chats = JSON.parse(xhttp.responseText).map((chat) => {
                    if (chat.init)
                        chat.date = new Date();
                        
                    return chat;
                });
                document.querySelector('#chat_header').innerHTML = 'DynamiChat';
                
                addMessages(chats);
            }
        }
        xhttp.send();
    }

getChats();

setInterval(() => {
  getChats();
}, 3000)


// window.onload = () => {
//     try {
//         var conn = new WebSocket('ws://localhost:8080');
//         conn.onmessage = (e) => {
//             console.log(e.data);
//         }
//         conn.onopen = (e) => {
//             console.log("Connection established!");
//         }
//     } catch (e) {
//         console.log(e);
//     }
// }

</script>
<style>
.message {
    margin-bottom: 10px;
}
.message_body {
    background-color: #007bff;
    border-radius: 5px;
    padding: 5px 10px 0 10px;
}
.message_status, message_date {
    display: inline;
}
.message_status {
    margin-left: 3px;
    display: none;
}
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="jumbotron">
            <div id="dynamichat">
                <div class="row justify-content-center">
                    <div class="dynamichat_header">
                    <h2 id="chat_header"></h2>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_messages col">
                    <div class="row">
                        <div class="message col" id="first_msg" style="display: none;">
                            <div class="row">
                                <div class="col-3">
                                    <img src="#" class="message_picture" alt="user_picture" style="display: none;">
                                </div>
                                <div class="col-6 message_body_container">
                                    <div class="message_body">
                                        <label class="message_text"></label>
                                        <br/>
                                        <label class="message_date"></label><label class="message_status">ads</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_inputs col">
                    <hr>
                    <div class="row">
                        <div class="col-9">
                            <input type="text" class="form-control" id="message"/>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

