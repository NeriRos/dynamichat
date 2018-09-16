var chatIds = [];
var allElementsAndChats = []
var support = {client: {}, representative: {}};
var chatClient, getChatsInterval, waiting, dynamichatEl, messageTemplate, messagesSection, repName, chatHeaderDescription;
var connection;

window.addEventListener('load', () => {
    if (window.location.pathname.endsWith("chatClient.php"))  {
        waiting = document.querySelector('#waiting');
        dynamichatEl = document.querySelector('#dynamichat');
        messageTemplate = dynamichatEl.querySelector('#first_msg');
        messagesSection = dynamichatEl.querySelector('.dynamichat_messages');
        repName = dynamichatEl.querySelector('#repName');
        chatHeaderDescription = dynamichatEl.querySelector('#chat_header_description');

        document.getElementById("message").addEventListener("keyup", (event) => {
            event.preventDefault();
            if (event.keyCode === 13) {
                sendMessage();
            }
        });
    }
}, false);

function init(support) {
    ajax("POST", "chat/v1/init", {support}, (_chatClient) => {
        chatClient = JSON.parse(_chatClient);

        getChats(true);

        initSocket(chatClient.support);
    });
}

function initSocket(localSupport) {
    try {
        connection = new WebSocket('ws://localhost:9000');
        connection.onmessage = (msg) => {
            var data = JSON.parse(msg.data);
            console.log("GOT DATA", data);

            if (data && data.representative && typeof data.representative !== "undefined" && data.representative != {}) { // Find Rep
                toggleWaitingRep(false, data.representative.name);
            } else if (data.init)  {
                console.log("CONNECTION ID:", data.connectionID);
                connection.send(JSON.stringify({init: true, getRepresentative: true, supportID: localSupport._id, user: localSupport.client}));
            }  else if (data.initialMessage)  {
                console.log("INITAL MESSAGE ", data);
                addMessages(data.message.message, true, data.message.support.representative);
            } else if (data.getConnectionID)  {
                console.log("CONNECTION ID:", data.connectionID);
                // connection.send(JSON.stringify({init: true, getRepresentative: true, supportID: localSupport._id, user: localSupport.client}));
            } else { // Got message
                var support = data.support || data.message.support;
                var message = data.message;

                if (support && message) {
                    addMessages(message, true, support.representative);
                } else {
                    console.log("MISSING DATA: support -", support, "message -", message, "Data:", data);
                }
            }
        }
        connection.onopen = (e) => {
            console.log("connection established!");
        }
    } catch (e) {
        console.log(e);
    }
}

function getChats(clean = true /*false*/) {
    ajax("GET", `chat/v1/getChats?userID=${support.client.id}&chatID=${support._id}`, null, (data) => {
        document.querySelector('#chat_header').innerHTML = 'DynamiChat';
        data = JSON.parse(data);

        toggleWaitingRep(!data.isAvailableRep);

        addMessages(data.chats || [], clean, data.representative);
    });
}

function sendMessage() {
    if (support) {
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
                _id: chatIds.length+1,
                sent: true
            };
            var elements = addMessages(data);
            msgElement.value = "";

            if (connection) {
                connection.send(JSON.stringify({message: data}));
            }
        }
    }
}

function addMessages(chats, clean = true, representative = false) {
    if (!Array.isArray(chats))
        chats = [chats];

    var newElements = [];

    for (var chat of chats) {
        if (chat && chat._id && chatIds.indexOf(chat._id) === -1) {
            var isChatClient = chat.from == support.client.id && !support.initial;
            var newMessageEl = messageTemplate.cloneNode(true);
            var picture = representative ? "/wp-content/plugins/dynamichat/assets/" + representative.picture + ".png" : '#';
            chat.date = new Date(chat.date);

            newMessageEl.id = chat._id;
            newMessageEl.setAttribute('data-date', chat.date.toTimeString());
            newMessageEl.querySelector('.message').classList.add(isChatClient ? "client" : "representative");
            newMessageEl.querySelector('.message_picture').setAttribute('src', picture);
            newMessageEl.querySelector('.message_text').innerHTML = chat.message;
            newMessageEl.querySelector('.message_date').innerHTML = chat.date.toTimeString().split(' ')[0];

            if (isChatClient)
                newMessageEl.querySelector('.message_body_container').classList.add("offset-6");

            chatIds.push(chat._id);

            allElementsAndChats.push({ element: newMessageEl, chat: chat });
            var index = newElements.push({ element: newMessageEl, chat: chat });

            refreshDom(messagesSection, allElementsAndChats, clean);

            changeStatus(newElements[index - 1]);

            if (chat.sent) {
                latestID = chat._id;
            }
        } else {
            console.log("chat already added or dosent exist");
        }

    }

    return newElements;
}

function refreshDom(section, elementsAndChats, clean) {
    if(elementsAndChats.length > section.childElementCount - 2) {
        var elementDates = [];
        if (clean) {
            while (section.firstChild) {
                section.removeChild(section.firstChild);
            }
        }
        elementsAndChats.sort((a, b) => {
            return new Date(a.chat.date) - new Date(b.chat.date);
        });
        elementsAndChats.forEach((elementAndChat) => {
            if (elementDates.indexOf(elementAndChat.element.getAttribute('data-date')) === -1) {
                section.append(elementAndChat.element);
                elementDates.push(elementAndChat.element.getAttribute('data-date'));
            }
        });
        elementsAndChats[elementsAndChats.length - 1].element.scrollIntoView();
    } else {
        console.log("NO NEW MESSAGES", elementsAndChats.length > section.childElementCount-1, elementsAndChats.length , section.childElementCount);
    }
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

        } else if (xhttp.readyState == 4) {
            cb(null, xhttp);
        }
    }
    xhttp.send(data && method == "POST" ? (typeof data === "string" ? data : JSON.stringify(data)) : "");
}

function toggleWaitingRep(isShow, representativeName = false) {
    waiting.style['display'] = isShow ? "flex" : "none";
    if (!isShow && representativeName) {
        repName.innerHTML = representativeName;
        chatHeaderDescription.style['display'] = isShow ? "none" : "inline";
    }
}


function OpenWindowWithPost(url, windowoption, name, params)
{
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", url);
    form.setAttribute("target", name);

    if (Object.keys(params).length == 1) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = Object.keys(params)[0];
        input.value = JSON.stringify(params[Object.keys(params)[0]]);
        form.appendChild(input);
    } else {
        for (var i in params) {
            if (params.hasOwnProperty(i)) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = i;
                input.value = params[i];
                form.appendChild(input);
            }
        }
    }

    document.body.appendChild(form);

    //note I am using a post.htm page since I did not want to make double request to the page
    //it might have some Page_Load call which might screw things up.
    window.open("post.htm", name, windowoption);

    form.submit();

    document.body.removeChild(form);
}

function openSupport(e, path)
{
    e.preventDefault();

    var user = {
        'name': document.querySelector('#chat-name').value,
        'business': document.querySelector('#chat-business').value,
        'phone': document.querySelector('#chat-phone').value
    };

    ajax("POST", "/wp-json/chat/v1/openSupport", user, (res, err) => {
        if (err) {
            console.log("error:", err.responseText);
        }
        var support = JSON.parse(res);

        if (support.support) {
            OpenWindowWithPost(path + "chatClient.php", "width=600,height=500,left=100,top=100,resizable=yes,scrollbars=yes", "DynamiChat-Client", support);
        } else {
            alert("There is no available representative..");
        }
    });

    return false;
}
function businessInputToggle(show) {
    document.querySelector(".chat-business-name-row").style.display = show ? "flex" : "none";
}