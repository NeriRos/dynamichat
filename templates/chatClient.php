<script>
    var chatIds = [];
    var support = {client: {}};
    var chatClient, getChatsInterval, waiting, dynamichatEl, messageTemplate, messagesSection;

    <?php
    if( isset( $_POST['support'] ) ) {
        echo 'support = ' . $_POST['support'] . ';';
    } else {
        echo 'console.log("no post data");';
        die;
    }
    ?>

    window.onload = () => {
        waiting = document.querySelector('#waiting');
        dynamichatEl = document.querySelector('#dynamichat');
        messageTemplate = dynamichatEl.querySelector('#first_msg');

        init();

        /*
            try {
                var conn = new WebSocket('ws://localhost:8080');
                conn.onmessage = (e) => {
                    console.log(e.data);
                }
                conn.onopen = (e) => {
                    console.log("Connection established!");
                }
            } catch (e) {
                console.log(e);
            }

            document.getElementById("message").addEventListener("keyup", (event) => {
                event.preventDefault();
                if (event.keyCode === 13) {
                    sendMessage();
                }
            });
        */
    }

    function init() {
        ajax("POST", "chat/v1/init", {support}, (_chatClient) => {
            chatClient = _chatClient;

            getChats(true);
        });
    }

    function getChats(clean = false) {
        ajax("GET", `chat/v1/messages?userID=${support.client.id}&chatID=${support._id}`, null, (data) => {
            document.querySelector('#chat_header').innerHTML = 'DynamiChat';
            data = JSON.parse(data);

            toggleWaitingRep(!data.isAvailableRep);

            addMessages(data.chats || [], clean);
        });
    }

    function sendMessage() {
        var msgElement = document.querySelector('#message');

        if (msgElement.value && msgElement.value.length > 0) {
            var data = {
                user: support.client,
                message: msgElement.value,
                from: support.client.id,
                date: new Date(),
                isSenderSelf: true,
                status: 0,
                id: support._id,
                chatID: chatIds.length+1
            };
            var elements = addMessages(data);
            msgElement.value = "";

            ajax("POST", "chat/v1/new_message", data, (resData) => {
                resData = JSON.parse(resData);
                var serverRes = resData.server;
                var message = resData.message;

                elements[0].chat.id = message.id;
                elements[0].chat.status = message.status;

                changeStatus(elements[0], message);
            });
        }
    }

    function addMessages(chats, clean = false) {
        if (!Array.isArray(chats))
            chats = [chats];

        var newElements = [];

        messagesSection = dynamichatEl.querySelector('.dynamichat_messages');

        for (var chat of chats) {
            if (chat && chatIds.indexOf(chat._id) === -1) {
                chat.date = new Date(chat.date);
                var isChatClient = chat.from == support.client.id && !support.initial;
                var newMessageEl = messageTemplate.cloneNode(true);

                newMessageEl.removeAttribute('id');
                newMessageEl.querySelector('.message').classList.add(isChatClient ? "client" : "representative");
                newMessageEl.querySelector('.message_picture').setAttribute('src', (chat.representative || {picture: "#"}).picture);
                newMessageEl.querySelector('.message_text').innerHTML = chat.message;
                newMessageEl.querySelector('.message_date').innerHTML = chat.date.toTimeString().split(' ')[0];

                if (isChatClient)
                    newMessageEl.querySelector('.message_body_container').classList.add("offset-6");

                chatIds.push(chat._id);

                var index = newElements.push({ element: newMessageEl, chat: chat });

                refreshDom(messagesSection, newElements, clean);

                changeStatus(newElements[index - 1]);

                if(typeof chat.id !== 'undefined')
                    latestID = chat.chatID;
            }

        }

        return newElements;
    }

    function refreshDom(section, elementsAndChats, clean) {
        if (clean) {
            while (section.firstChild) {
                section.removeChild(section.firstChild);
            }
        }

        elementsAndChats.sort((a, b) => {
            return new Date(a.chat.date) - new Date(b.chat.date);
        });
        elementsAndChats.forEach((elementAndChat) => {
            section.append(elementAndChat.element);
        });
        elementsAndChats[elementsAndChats.length - 1].element.scrollIntoView();
    }

    function changeStatus(datas) {
        if(!Array.isArray(datas))
            datas = [datas];

        for (var data of datas) {
            if (data.element) {
                var statusEl = data.element.querySelector('.message_status');

                switch(data.chat.status) {
                    case 0:
                        statusEl.innerHTML = '';
                        break;
                    case 1:
                        statusEl.innerHTML = '<i class="fa fa-check"></i>';
                        break;
                    case 2:
                        statusEl.innerHTML = '<i class="fa fa-check-double"></i>';
                        break;
                }
            } else {
                console.log("NO ELEMENT");
            }
        }
    }

    function ajax(method, url, data, cb) {
        var xhttp = new XMLHttpRequest();

        xhttp.open(method, (url.startsWith('/') ? url : "/wp-json/"+url), true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.onreadystatechange = () => {
            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                if (cb)
                    cb(xhttp.responseText);

            }
        }
        xhttp.send(data && method == "POST" ? JSON.stringify(data) : "");
    }

    function toggleWaitingRep(isShow) {
        waiting.style['display'] = isShow ? "flex" : "none";
    }

    if (getChatsInterval)
        clearInterval(getChatsInterval);

    getChatsInterval = setInterval(() => {
        getChats();
    }, 5000);

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
    .dynamichat_container {
        margin-top: 5px;
    }
    .dynamichat_messages {
        height: 230px;
        overflow-y: scroll;
    }
    #waiting {
        position: absolute;
        bottom: 0;
    }

    #first_msg {
        display: none;
    }
    .message {
        display: block;
    }
    .message.client .message_picture {
        display: none;
    }
    .message.client .message_status {
        display: inline;
    }
    .message.representative .message_picture {
        display: block;
    }
    .message.representative .message_body {
        float: left !important;
        background-color: #949da5;
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="jumbotron dynamichat_container">
            <div id="dynamichat">
                <div class="row justify-content-center">
                    <div class="dynamichat_header">
                    <h2 id="chat_header"></h2>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_messages col">
                    <div class="row message_row" id="first_msg">
                        <div class="message col">
                            <div class="row">
                                <div class="col-2">
                                    <img src="#" class="message_picture" alt="user_picture">
                                </div>
                                <div class="col-6 message_body_container">
                                    <div class="message_body">
                                        <label class="message_text"></label>
                                        <br/>
                                        <label class="message_date"></label><label class="message_status"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label id="waiting" class="text-muted" style="display: none">waiting representative to join..</label>
                </div>
            </div>
            <div class="row">
                <div class="dynamichat_inputs col">
                    <hr>
                    <div class="row">
                        <div class="col-10">
                            <input type="text" class="form-control" id="message" />
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

