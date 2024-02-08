import Blockchain from './blockchain/blockchain';
import Wallet from './wallet/wallet';

// Assuming Blockchain and Wallet classes are already defined and available

document.addEventListener('DOMContentLoaded', () => {
    const blockchain = new Blockchain();
    const wallet = new Wallet(); // This should be replaced with actual wallet initialization

    // Display wallet information
    document.getElementById('wallet-address')!.textContent = wallet.publicKey;
    document.getElementById('wallet-balance')!.textContent = '100'; // Placeholder balance

    // Display the blockchain
    document.getElementById('blockchain-content')!.textContent = JSON.stringify(blockchain.chain, null, 2);
});
