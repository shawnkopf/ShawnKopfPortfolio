import WebSocket from 'ws';
import Blockchain from '../blockchain/blockchain';

class P2PNetwork {
    private sockets: WebSocket[];
    blockchain: Blockchain;

    constructor(blockchain: Blockchain) {
        this.blockchain = blockchain;
        this.sockets = [];
    }

    // Initialize the P2P server on a port
    createServer(port: number) {
        const server = new WebSocket.Server({ port: port });
        server.on('connection', (socket) => this.connectSocket(socket));
        console.log(`Listening for peer-to-peer connections on: ${port}`);
    }

    // Connect to peers (other nodes) given an array of addresses
    connectToPeers(peers: string[]) {
        peers.forEach((peer) => {
            const socket = new WebSocket(peer);
            socket.on('open', () => this.connectSocket(socket));
            socket.on('error', (error) => console.log('Connection failed:', error));
        });
    }

    // Handle new socket connection
    private connectSocket(socket: WebSocket) {
        this.sockets.push(socket);
        console.log('Socket connected');
        this.initMessageHandler(socket);
        this.sendChain(socket);
    }

    // Initialize message handler for a socket
    private initMessageHandler(socket: WebSocket) {
        socket.on('message', (data) => {
            const message = JSON.parse(data.toString());
            this.handleMessage(socket, message);
        });
    }

    // Send the current blockchain chain to a socket
    private sendChain(socket: WebSocket) {
        socket.send(JSON.stringify({ type: 'chain', data: this.blockchain.chain }));
    }

    // Handle messages received from a socket
    private handleMessage(socket: WebSocket, message: any) {
        switch (message.type) {
            case 'chain':
                this.blockchain.replaceChain(message.data);
                break;
            // Add more cases as needed, such as handling transactions
        }
    }

    // Broadcast the latest chain to all connected sockets
    broadcastChain() {
        this.sockets.forEach((socket) => this.sendChain(socket));
    }
}

export default P2PNetwork;
