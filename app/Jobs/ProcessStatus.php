<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Deposit;

class ProcessStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deposit_id;
    protected $client_url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deposit_id, $client_url)
    {
        $this->deposit_id = $deposit_id;
        $this->client_url = $client_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $test = Deposit::find($this->deposit_id); print_r($test->order_id);
        if ($test->status == 'incomplete') {
            // status inquiry
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->client_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    "ORDER_ID" => $test->order_id,
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $json_resp = json_decode($response);
            print_r($json_resp); exit();
            $test->status = $json_resp->STATUS;
            $test->save();
        }
    }
}
