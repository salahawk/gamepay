require('dotenv').config();
const API_URL = process.env.API_URL;
const ADMIN_WALLET = process.env.ADMIN_WALLET;
const ADMIN_PRIVATE = process.env.ADMIN_PRIVATE;
const OPERATOR_WALLET = process.env.OPERATOR_WALLET;
const FEE_WALLET = process.env.FEE_WALLET;
const RESERVE_WALLET = process.env.RESERVE_WALLET;

const abi =  require("./contract-abi.json");
const Web3 = require('like-web3');
const { Contracts } = require('like-web3');

const web3 = new Web3({
    provider: "https://data-seed-prebsc-1-s1.binance.org:8545/",
    network: 'bsc-testnet',
    privateKey: ADMIN_PRIVATE 
})


const contractAddress = "0xfc714A248F7170674955B7f1BEBA13E0690E62F8";

Contracts.add('aaaa', {
    address: contractAddress, 
    abi
})

let merchant = process.argv[2];
let amountString = process.argv[3];
let num = process.argv[4];
amount = parseFloat(amountString) * 1e18;

// set operator
async function setOperator(merchant) {
    console.log(await web3.contract('aaaa').admin_fee_wallet());
    web3.nonce = await web3.getTransactionCount(web3.address)

    await web3.send('aaaa', {
        method: "add_operator_merchant",
        args: [OPERATOR_WALLET,merchant],
        to: "aaaa",
        nonce: web3.nonce
    })
}

// add fee wallet
async function setFeeWallet(merchant) {
    web3.nonce = await web3.getTransactionCount(web3.address)

    await web3.send('aaaa', {
        method: "set_admin_fee_wallet",
        args: [merchant],
        to: "aaaa",
        nonce: web3.nonce
    })
}

// mint
async function mint(merchant, amount) {
    web3.nonce = await web3.getTransactionCount(web3.address)

    await web3.send('aaaa', {
        method: "mint",
        args: [merchant, amount],
        to: "aaaa",
        nonce: web3.nonce
    })
}

if (num != 0) mint(merchant, amount);
else {
    setOperator(merchant);
    mint(merchant, amount);
}
