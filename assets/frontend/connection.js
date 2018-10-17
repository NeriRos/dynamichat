class Connection {
    constructor(socket) {
        this.socket = socket;
        this.nodeConnectionId = -1;
    }

    sendServerMessage(message, event) {
        message.event = event;
        message.nodeConnectionId = this.nodeConnectionId;

        if(this.socket.readyState === 1) {
            Connection.sendSocketMessage(this.socket, message);
        } else {
            console.log("Socket is not open..", this.socket.readyState);
        }
    }

    sendServerInit() {
        if(this.socket.readyState === 3) {
            Connection.sendSocketMessage(this.socket, {event: window.SOCKET_EVENTS.SUPPORT_INIT});
        } else {
            console.log("Socket is not open..", this.socket.readyState);
        }
    }

    setNodeConnectionId(nodeConnectionId) {
        this.nodeConnectionId = nodeConnectionId;
    }

    static sendSocketMessage(socket, data) {
        socket.send(JSON.stringify(data));
    }
}