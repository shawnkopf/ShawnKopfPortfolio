import * as crypto from 'crypto';

class Transaction {
    id: string;
    fromAddress: string | null;
    toAddress: string;
    amount: number;
    signature: string | null;

    constructor(fromAddress: string | null, toAddress: string, amount: number) {
        this.id = this.calculateHash();
        this.fromAddress = fromAddress;
        this.toAddress = toAddress;
        this.amount = amount;
        this.signature = null; // This will be replaced with a real signature
    }

    calculateHash(): string {
        return crypto.createHash('sha256').update(this.fromAddress + this.toAddress + this.amount).digest('hex');
    }

    signTransaction(signingKey: any) {
        if (signingKey.getPublic('hex') !== this.fromAddress) {
            throw new Error('You cannot sign transactions for other wallets!');
        }

        // Placeholder for signing logic
        // In a real implementation, you would use signingKey to sign the hash of the transaction
        // For example: this.signature = signingKey.sign(this.calculateHash(), 'base64').toDER('hex');
        this.signature = 'signature-placeholder';
    }

    isValid(): boolean {
        if (this.fromAddress === null) return true; // Mining rewards

        if (!this.signature || this.signature.length === 0) {
            throw new Error('No signature in this transaction');
        }

        // Placeholder for real verification logic
        // In a real implementation, you would verify the signature with the fromAddress (public key)
        // For example, using elliptic library: EC.verify(this.calculateHash(), this.signature, this.fromAddress, 'hex')
        return true; // Simplified for this example
    }
}

export default Transaction;

