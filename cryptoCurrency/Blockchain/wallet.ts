class Wallet {
    publicKey: string;
    privateKey: string;

    constructor() {
        const keypair = generateKeypair();
        this.publicKey = keypair.publicKey;
        this.privateKey = keypair.privateKey;
    }

    generateKeypair(): any {
        // Placeholder for key generation logic
        return { publicKey: 'publicKey', privateKey: 'privateKey' };
    }
}

export default Wallet;
import Block from './block';

class Blockchain {
    chain: Block[];
    difficulty: number;

    constructor() {
        this.chain = [this.createGenesisBlock()];
        this.difficulty = 2; // Adjust for proof of work difficulty
    }

    createGenesisBlock(): Block {
        return new Block([], '0');
    }

    getLatestBlock(): Block {
        return this.chain[this.chain.length - 1];
    }

    addBlock(newBlock: Block) {
        newBlock.prevHash = this.getLatestBlock().hash;
        newBlock.mineBlock(this.difficulty);
        this.chain.push(newBlock);
    }

    isChainValid(): boolean {
        for (let i = 1; i < this.chain.length; i++) {
            const currentBlock = this.chain[i];
            const prevBlock = this.chain[i - 1];

            if (currentBlock.hash !== currentBlock.calculateHash()) {
                return false;
            }

            if (currentBlock.prevHash !== prevBlock.hash) {
                return false;
            }
        }
        return true;
    }
}

export default Blockchain;
