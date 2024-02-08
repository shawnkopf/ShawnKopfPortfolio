import * as crypto from "crypto";

class Block {
  timestamp: number;
  transactions: any[];
  prevHash: string;
  hash: string;
  nonce: number;

  constructor(transactions: any[], prevHash = "") {
    this.timestamp = Date.now();
    this.transactions = transactions;
    this.prevHash = prevHash;
    this.hash = this.calculateHash();
    this.nonce = 0;
  }

  calculateHash(): string {
    return crypto
      .createHash("sha256")
      .update(
        this.prevHash +
          this.timestamp +
          JSON.stringify(this.transactions) +
          this.nonce
      )
      .digest("hex");
  }

  // mineBlock(difficulty: number) {
  //     while (this.hash.substring(0, difficulty) !== Array(difficulty + 1).join('0')) {
  //         this.nonce++;
  //         this.hash = this.calculateHash();
  //     }
  //     console.log("Block mined: " + this.hash);
  // }
}

export default Block;
