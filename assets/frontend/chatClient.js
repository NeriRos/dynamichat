var chatClient, getChatsInterval, waiting, dynamichatEl, messageTemplate, messagesSection, repName, chatHeaderDescription;

var websocketServerUri = `wss://wsphp.cargo-express.co.il`;

window.chatIds = [];
window.allElementsAndChats = []
window.support = {client: {}, representative: {}};
window.user = window.support.client;
window.SOCKET_EVENTS = {
    MISSIONS_INIT: "innerChatInit",

    CHAT_INIT: "chatInit",
    CHAT_MESSAGE: "chatMessage",

    SUPPORT_INIT: "supportInit",
    SUPPORT_MESSAGE: "supportMessage",

    MESSAGE_CALLBACK: "messageCallback",
    ERROR: "error",

    MESSAGE_READ: "messageRead",
    GET_CONNECTION_ID: "getConnectionID",

    GET_REPRESENTATIVE: "getRepresentative",
    CLIENT_MESSAGE: "clientMessage",
    FIND_AVAILABLE_REP: "findAvailableRep"
};

window.addEventListener('load', () => {
    // set elements pointers
    waiting = document.querySelector('#waiting');
    dynamichatEl = document.querySelector('#dynamichat');
    messageTemplate = dynamichatEl.querySelector('#first_msg');
    messagesSection = dynamichatEl.querySelector('.dynamichat_messages');
    repName = dynamichatEl.querySelector('#repName');
    chatHeaderDescription = dynamichatEl.querySelector('#chat_header_description');

    init(window.support);

    document.querySelector('#chat_header').innerHTML = 'DynamiChat';

    // send message on enter press in message input
    document.getElementById("message").addEventListener("keyup", (event) => {
        event.preventDefault();
        if (event.keyCode === 13) {
            sendMessage();
        }
    });
}, false);

/**
 * send init request to server.
 * get back chatClient object with support.
 * get chats from server (if any), init socket methods.
 * @param {ISupport} support object from server.
 */
function init(support) {
    ajax("POST", "chat/v1/init", {support}, (_chatClient) => {
        chatClient = JSON.parse(_chatClient);
        window.support = chatClient.support;
        window.user = chatClient.support.client;

        getChats(true);

        initSocket();
    });
}

/**
 * open new socket connection to server, and set socket methods.
 * route all socket messages to SocketMessageManager
 *
 * on socket open, send SUPPORT_INIT event to server,
 * with {support: ISupport, user: IUser}.
 */
function initSocket() {
    try {
        window.connection = new Connection(new WebSocket(websocketServerUri), window.user);
        window.connection.socket.onmessage = (msg) => {
            var data = JSON.parse(msg.data);

            const socketMessageManager = new SocketMessageManager(data);
        };
        window.connection.socket.onopen = (e) => {
            console.log("connection established!");
            window.connection.sendServerInit(window.support);
        };
    } catch (e) {
        console.log(e);
    }
}

/**
 * get chats for support from server.
 * @param {boolean} clean is delete all message elements, before adding new ones.
 */
function getChats(clean = true) {
    ajax("GET", `chat/v1/getChats?userID=${window.support.client.id}&chatID=${window.support._id}`, null, (data) => {
        data = JSON.parse(data);

        if (data) {
            toggleWaitingRep(!data.isAvailableRep);

            addMessages(data.chats || [], clean, data.representative);
        }
    });
}

function sendMessage() {
    if (window.support) {
        var msgElement = document.querySelector('#message');

        if (msgElement.value && msgElement.value.length > 0) {
            var message = {
                user: window.user,
                message: msgElement.value,
                from: window.user.id,
                date: new Date(),
                isSenderSelf: true,
                status: 0,
                id: window.support._id,
                chatId: window.chatIds.length,
                sent: true,
                contact: (window.support.representative || {id: ""}).id
            };

            var data = {
                support: window.support,
                supportId: window.support._id,
                chat: {
                    message: msgElement.value,
                    from: window.user.id,
                    date: new Date(),
                    status: 0,
                    chatId: window.chatIds.length,
                    contact: (window.support.representative || {id: ""}).id
                }
            };

            if (window.connection) {
                window.connection.sendServerMessage(data, window.SOCKET_EVENTS.CLIENT_MESSAGE);
                var elements = addMessages(message);
                msgElement.value = "";
            }
        }
    }
}

function addMessages(chats, clean = true, representative = false) {
    if (!Array.isArray(chats))
        chats = [chats];

    var newElements = [];

    for (var chat of chats) {
        chat.chatId = chat._id || chat.chatId;

        if (window.chatIds.indexOf(chat.chatId) === -1) {
            var isChatClient = chat.from == window.support.client.id && !window.support.initial;
            var newMessageEl = messageTemplate.cloneNode(true);
            var picture = representative ? "/wp-content/plugins/dynamichat/assets/" + representative.picture + ".png" : '#';
            chat.date = new Date(chat.date);

            newMessageEl.id = chat.chatId;
            newMessageEl.setAttribute('data-date', chat.date.toTimeString());
            newMessageEl.querySelector('.message').classList.add(isChatClient ? "client" : "representative");
            newMessageEl.querySelector('.message_picture').setAttribute('src', picture);
            newMessageEl.querySelector('.message_text').innerHTML = chat.message;
            newMessageEl.querySelector('.message_date').innerHTML = chat.date.toTimeString().split(' ')[0];

            if (isChatClient)
                newMessageEl.querySelector('.message_body_container').classList.add("offset-6");

            window.chatIds.push(chat.chatId);

            window.allElementsAndChats.push({ element: newMessageEl, chat: chat });
            var index = newElements.push({ element: newMessageEl, chat: chat });

            refreshDom(messagesSection, window.allElementsAndChats, clean);

            changeStatus(newElements[index - 1]);

            if (chat.sent) {
                latestID = chat.chatId;
            }
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

function toggleWaitingRep(isShow, representativeName = false) {
    waiting.style['display'] = isShow ? "flex" : "none";
    if (!isShow && representativeName) {
        repName.innerHTML = representativeName;
        chatHeaderDescription.style['display'] = isShow ? "none" : "inline";
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

function getStatus(status) {
    status = status === 0 ? "" : status === 1 ? "fa-check" : status === 2 ? "fa-check-double" : "";
    status = status.length <= 0 ? "" : '<i class="fa ' + status + '"></i>';

    return status;
}