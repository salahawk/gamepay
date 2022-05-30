<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Deposit;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Checks if the given string is an address
     *
     * @method isAddress
     * @param {String} $address the given HEX adress
     * @return {Boolean}
     */
    public function isAddress($address)
    {
        if (!preg_match('/^(0x)?[0-9a-f]{40}$/i', $address)) {
            // check if it has the basic requirements of an address
            return false;
        } elseif (!preg_match('/^(0x)?[0-9a-f]{40}$/', $address) || preg_match('/^(0x)?[0-9A-F]{40}$/', $address)) {
            // If it's all small caps or all all caps, return true
            return true;
        } else {
            // Otherwise check each case
            return $this->isChecksumAddress($address);
        }
    }

    /**
     * Checks if the given string is a checksummed address
     *
     * @method isChecksumAddress
     * @param {String} $address the given HEX adress
     * @return {Boolean}
     */
    public function isChecksumAddress($address)
    {
        // Check each case
        $address = str_replace('0x', '', $address);
        $addressHash = hash('sha3', strtolower($address));
        $addressArray = str_split($address);
        $addressHashArray = str_split($addressHash);

        for ($i = 0; $i < 40; $i++) {
            // the nth letter should be uppercase if the nth digit of casemap is 1
            if ((intval($addressHashArray[$i], 16) > 7 && strtoupper($addressArray[$i]) !== $addressArray[$i]) || (intval($addressHashArray[$i], 16) <= 7 && strtolower($addressArray[$i]) !== $addressArray[$i])) {
                return false;
            }
        }
        return true;
    }

    public function ifscCheck($ifsc)
    {
        if (!preg_match('/^[A-Za-z]{4}0[A-Z0-9a-z]{6}$/', $ifsc)) {
            // check if it has the basic requirements of an address
            return false;
        } else {
            return true;
        }
    }

    public function upiCheck($upi)
    {
        if (!preg_match('/^\w.+@\w+$/', $upi)) {
            // check if it has the basic requirements of an address
            return false;
        } else {
            return true;
        }
    }

    public function mint_token($deposit_id, $amount)
    {
        $deposit = Deposit::find($deposit_id);
        $exec_phrase =
            'node contract-interact.js ' . $deposit->wallet . ' ' . $amount;

        chdir('../');
        exec($exec_phrase, $var, $result);
        if ($result) {
            return response()->json(['status' => 'fail', 'message' => "Deposit succeeded but mint failed"]);
        }

        $mint_status = '';
        $mint_comment = '';
        $crypto_txn_hash = '';
        $mint_error = "";
        $real_value = "";
        foreach ($var as $item) {
            if (str_contains($item, "mintHash")) {
                $mint_status = "Success";
                $crypto_txn_hash = substr($item, -66);
            }

            if (str_contains($item, "realValue")) {
                $real_value = substr($item, 10);
            }

            if (str_contains($item, "error")) {
                $mint_error = $item;
                $mint_status = "Fail";
            }
        }

        $deposit->mint_status = $mint_status;
        $deposit->mint_comment = $mint_comment;
        $deposit->crypto_txn_hash = $crypto_txn_hash;
        $deposit->mint_error = $mint_error;
        $deposit->actual_amount = $real_value;
        $deposit->save();
    }
}
