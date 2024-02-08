import Block from './block';
import * as crypto from 'crypto';

/**
 * Performs the proof-of-work for the given block.
 * @param {Block} block - The block to mine.
 * @param {number} difficulty - The difficulty level of the proof-of-work algorithm.
 */
export function mineBlock(block: Block, difficulty: number): void {
    // Condition to satisfy: find a hash that starts with 'difficulty' number of '0's
    const target = Array(difficulty + 1).join('0');
    while (block.hash.substring(0, difficulty) !== target) {
        block.nonce++;
        block.hash = calculateBlockHash(block);
    }
    console.log(`Block successfully mined: ${block.hash}`);
}

/**
 * Calculates the hash of the given block.
 * @param {Block} block - The block to hash.
 * @returns {string} - The hash of the block.
 */
function calculateBlockHash(block: Block): string {
    const { timestamp, transactions, prevHash, nonce } = block;
    const blockContent = timestamp + JSON.stringify(transactions) + prevHash + nonce;
    return crypto.createHash('sha256').update(blockContent).digest('hex');
}
