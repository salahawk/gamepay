require('dotenv').config();
const API_URL = process.env.API_URL;
const ADMIN_WALLET = process.env.ADMIN_WALLET;
const ADMIN_PRIVATE = process.env.ADMIN_PRIVATE;
const OPERATOR_WALLET = process.env.OPERATOR_WALLET;
const FEE_WALLET = process.env.FEE_WALLET;
const RESERVE_WALLET = process.env.RESERVE_WALLET;

const abi =  require("./contract-abi.json");
// const Web3 = require('like-web3');
// const { Contracts } = require('like-web3');

const Web3 = require('web3');

const web3 = new Web3("https://data-seed-prebsc-1-s1.binance.org:8545/");

// const web3 = new Web3({
//     provider: "https://data-seed-prebsc-1-s1.binance.org:8545/",
//     network: 'bsc-testnet',
//     privateKey: ADMIN_PRIVATE 
// })

web3.eth.accounts.wallet.add(ADMIN_PRIVATE);


const contractAddress = "0xfc714A248F7170674955B7f1BEBA13E0690E62F8";

let contract = new web3.eth.Contract(abi, contractAddress);

let merchant = process.argv[2];
let amountString = process.argv[3]; 
// let num = process.argv[4];
amount = web3.utils.toWei(amountString);

// set operator
async function setOperator(merchant) {
    // console.log(await web3.contract('aaaa').admin_fee_wallet());
    // web3.nonce = await web3.getTransactionCount(web3.address)

    // console.log(web3.nonce);
    // await web3.send('aaaa', {
    //     method: "add_operator_merchant",
    //     args: [OPERATOR_WALLET,merchant],
    //     to: "aaaa",
    //     nonce: web3.nonce
    // })
    const gas = await contract.methods.add_operator_merchant(OPERATOR_WALLET, merchant).estimateGas({from: ADMIN_WALLET});
    await contract.methods.add_operator_merchant(OPERATOR_WALLET, merchant).send({from: ADMIN_WALLET, gas: gas});
}

async function checkHaveoperator(merchant) {
    let operator = await contract.methods.merchant_to_operator(merchant).call();
    if (operator != "0x0000000000000000000000000000000000000000") {
        return true;
    } else {
        return false;
    }
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
    // web3.nonce = await web3.getTransactionCount(web3.address)
    // console.log("nonce", web3.nonce);
    // await web3.send('aaaa', {
    //     method: "mint",
    //     args: [merchant, amount],
    //     to: "aaaa",
    //     nonce: web3.nonce
    // })
    const gas = await contract.methods.mint(merchant, amount).estimateGas({from: ADMIN_WALLET});
    await contract.methods.mint(merchant, amount).send({from: ADMIN_WALLET, gas: gas});
}

void async function main() {
    let check = await checkHaveoperator(merchant);
    if (check) { 
        await mint(merchant, amount);
    } else {
        await setOperator(merchant);
        console.log("hey, setOperator");
        await mint(merchant, amount);
    }
}()


