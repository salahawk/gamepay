<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Deposit;
use App\Models\External;
use App\Models\Payout;

class ChooseGateway
{
    public function choose_gateway($email, $loyalty_points = null)
    {
        $salt_string =
            'E%EkuMuWcv@cdjphcFLzrENe0&sGWCyYeNaE7YtdLkXM763@WS6k1sxEk@V18hCHOi2hLc1726fqY2UJ9X3OhSGLFJy@nf%yz2z';
        $user_unique_id = (int) crc32($salt_string . $email);
        $user_db_loyalty_points = 0;

        $exists_record_flag = 0;
    
        $paymentinfo = [];
    
        $details_json = '';
    
        $user_level = 1;

            $user_current_loyalty_points =
            $loyalty_points !== null
                ? $loyalty_points
                : loyalty_points_api($email);

        $user = External::where('email', $email)->first();
        if (!empty($user)) {
            $user_db_loyalty_points = $user->loyalty_points;
            $exists_record_flag = 1;
        } else {
        }

        if ($user_current_loyalty_points > $user_db_loyalty_points) {
            $user_loyalty_points = $user_current_loyalty_points;
        } else {
            $user_loyalty_points = $user_db_loyalty_points;
        }

        $user_level = calculate_level($email, $user_loyalty_points);

        // if ($exists_record_flag) {
        //     if ($details_json['level_' . $user_level . '_' . $pay_mode] == '') {
        //         $pg_choice = -1;
        //     } else {
        //         $pg_choice =
        //             $details_json['level_' . $user_level . '_' . $pay_mode];
        //     }
    
        //     if ($real_company_id == '') {
        //         $real_company_id = -1;
        //     }
        // }
        $pg_choice = confirm_pg_choice(
            $email,
            $pg_choice,
            $user_level,
            $user_loyalty_points,
            $user_unique_id,
            $pay_mode,
            $real_company_id,
            $conn
        );


    }

    private function loyalty_points_api($email)
    {
        $encodeEmail = urlencode($email);

        // Get User Loyalty points
        $response = get_web_page(
            "https://www.jungleraja.com/api/v1/crmGetInfo?email=$encodeEmail"
        );
        $content = $response['content'];
        $result = json_decode($content);

        $loyalty_points = $result ? $result->response->loyalty->TotalPoints : 0;

        return $loyalty_points;
    }

    private function calculate_level($email, $loyalty_points)
    {
        $total_levels = 10;

        $user_level = 1;

        $user_loyalty_points = $loyalty_points;

        $email = trim($email);

        $time_loyalty_level = get_time_loyalty_level(
            $email,
            $loyalty_points,
            $conn
        );

        $points_loyalty_level = get_points_loyalty_level(
            $email,
            $loyalty_points,
            $conn,
            0
        );

        if (
            $time_loyalty_level > 1 &&
            $time_loyalty_level >= $points_loyalty_level
        ) {
            $user_level = $time_loyalty_level;
        } elseif (
            $points_loyalty_level > 1 &&
            $points_loyalty_level >= $time_loyalty_level
        ) {
            $user_level = $points_loyalty_level;
        }

        return $user_level;
    }

    private function get_time_loyalty_level($email, $user_loyalty_points)
    {
        $time_loyalty_level = 1;

        //Increase this variable if you want to  make it difficult for older users to level up

        $user = External::where('email', $email)->first();
        if (!empty($user)) {
            $cur_user_id = $user->id;
        } else {
            return 1;
        }

        $row_count = External::count();

        if ($row_count > 0) {
            // output data of each row

            $total_levels = 10;

            $total_rows = $row_count;

            $cur_limit = 1;

            $cur_lower_bound = 0;

            $cur_upper_bound = 0;
            
            $cur_high_id = 0;
            
            $cur_low_id = 0;

            while($cur_limit <= 10) {
              $cur_upper_bound = ceil(($total_rows * $cur_limit) / $total_levels);
            
              $row = External::orderBy('id', 'DESC')->skip($cur_lower_bound)->take(1)->get()->toArray();
            
              $cur_high_id = $row->id;
            
              $row = External::orderBy('id', 'DESC')->skip($cur_upper_bound)->take(1)->get()->toArray();
            
              $cur_low_id = $row->id;
            
              if ($cur_user_id >= $cur_low_id && $cur_user_id <= $cur_high_id) {
                  return get_points_loyalty_level(
                      $email,
                      $user_loyalty_points,
                      $cur_limit
                  );
              }
              
              $cur_limit++;
            }
        } else {
            return 1;
        }
    }

    private function get_points_loyalty_level($email, $user_loyalty_points, $time_loyalty_level) {
      $avg_loyalty_points = get_points_loyalty_level_avg($time_loyalty_level);
  
      $return_val = 0;
  
      $level = 0;
  
      for ($i = 1; $i <= count($avg_loyalty_points); $i++) {
          $level++;
  
          if ($user_loyalty_points <= $avg_loyalty_points[$i]) {
              return $i;
          }
      }
  
      return 10;
    }

    private function get_points_loyalty_level_avg($time_loyalty_level) {
        $cur_limit = 1;
        $cur_lower_bound = 0;
        $loyalty_divider = 10;
        $avg_loyalty_points = [];
    
        $apcu_key =
            'get_points_loyalty_level_avg_' .
            ($time_loyalty_level ? $time_loyalty_level : 0);
        if (apcu_exists($apcu_key)) {
            $cache_result = apcu_fetch($apcu_key);
            if (!empty($cache_result)) {
                return $cache_result;
            }
        }
    
        if ($time_loyalty_level) {
            $loyalty_divider *= $time_loyalty_level;
        }
        
        $avg_loyalty_point = External::where('loyalty_points', '>=', $cur_lower_bound)->avg('loyalty_points');

        $avg_loyalty_points[$cur_limit] = $avg_loyalty_point;
        
        while($cur_limit <= 10) {

          $cur_limit++;

          $cur_lower_bound = $avg_loyalty_points[$cur_limit - 1];
      
          $cur_lower_bound /= $loyalty_divider;
      
          $avg_loyalty_point = External::where('loyalty_points', '>=', $cur_lower_bound)->avg('loyalty_points');

          $avg_loyalty_points[$cur_limit] = $avg_loyalty_point;
        }
        

        $apcu_result = apcu_store($apcu_key, $avg_loyalty_points, 300);
    
        return $avg_loyalty_points;
    }

    private function confirm_pg_choice($email, $pg_choice, $user_level, $user_loyalty_points, $user_unique_id, $pay_mode, $real_company_id) {
        $bounds_arr = get_bounds($user_level);
    
        $priority_lower_bound = $bounds_arr[0];
    
        $priority_upper_bound = $bounds_arr[1];
        
        $new_pg_choice = check_active_gateway(
            $email,
            $pg_choice,
            $user_level,
            $user_loyalty_points,
            $user_unique_id,
            $pay_mode,
            $real_company_id,
            $conn
        );
    
        return $new_pg_choice;
    }

    private function get_bounds($user_level) {
        if ($user_level == 2) {
            $priority_lower_bound = 1000;

            $priority_upper_bound = 1999;
        } else if ($user_level == 3) {
            $priority_lower_bound = 2000;

            $priority_upper_bound = 2999;
        } else if ($user_level == 4) {
            $priority_lower_bound = 3000;

            $priority_upper_bound = 3999;
        } else if ($user_level == 5) {
            $priority_lower_bound = 4000;

            $priority_upper_bound = 4999;
        } else if ($user_level == 6) {
            $priority_lower_bound = 5000;

            $priority_upper_bound = 5999;
        } else if ($user_level == 7) {
            $priority_lower_bound = 6000;

            $priority_upper_bound = 6999;
        } else if ($user_level == 8) {
            $priority_lower_bound = 7000;

            $priority_upper_bound = 7999;
        } else if ($user_level == 9) {
            $priority_lower_bound = 8000;

            $priority_upper_bound = 8999;
        } else if ($user_level == 10) {
            $priority_lower_bound = 9000;

            $priority_upper_bound = 9999;
        } else {
            $priority_lower_bound = 0;

            $priority_upper_bound = 999;
        }

        return [$priority_lower_bound, $priority_upper_bound];
    }

    private function check_active_gateway($email, $pg_choice, $user_level, $user_loyalty_points, $user_unique_id, $pay_mode, $real_company_id) {
        global $regionQuery;
    
        $bounds_arr = get_bounds($user_level);
    
        $priority_lower_bound = $bounds_arr[0];
    
        $priority_upper_bound = $bounds_arr[1];
    
        $new_pg_choice = $pg_choice;
    
        // $new_real_company_id = $real_company_id;
    
        $disbled_gateway_arr = check_disabled_gateway(
            $pg_choice,
            $user_unique_id,
            $pay_mode,
            $conn
        );
    
        if ($disbled_gateway_arr[0]) {
            return $disbled_gateway_arr[1];
        }
    
        $sql = "SELECT * FROM `tbl_payment_gateways` WHERE id = $pg_choice AND status = 1 AND priority >= $priority_lower_bound AND priority <= $priority_upper_bound AND payment_mode LIKE '%$pay_mode%' AND real_company_id = '$real_company_id' $regionQuery";
    
        $result = $conn->query($sql);
    
        $row_count = $result->num_rows;
    
        if ($row_count) {
            $new_pg_choice = $pg_choice;
    
            $new_real_company_id = $real_company_id;
        } else {
            $sql = "SELECT * FROM `tbl_payment_gateways` WHERE id = $pg_choice AND status = 1 AND priority >= $priority_lower_bound AND priority <= $priority_upper_bound AND payment_mode LIKE '%$pay_mode%' $regionQuery";
    
            $result = $conn->query($sql);
    
            $row_count = $result->num_rows;
    
            if ($row_count) {
                $sql = "SELECT * FROM `tbl_payment_gateways` WHERE status = 1 AND priority >= $priority_lower_bound AND priority <= $priority_upper_bound AND payment_mode LIKE '%$pay_mode%' AND real_company_id = '$real_company_id' $regionQuery";
    
                $result = $conn->query($sql);
    
                $row_count = $result->num_rows;
    
                if ($row_count) {
                    $segment_counter = $user_unique_id % $row_count;
    
                    while ($row = $result->fetch_assoc()) {
                        if ($segment_counter) {
                            $segment_counter--;
                        } else {
                            $new_pg_choice = $row['id'];
    
                            $new_real_company_id = $row['real_company_id'];
                        }
                    }
                } else {
                    //look for gateways with preferred company id
    
                    $new_pg_choice = $pg_choice;
    
                    $new_real_company_id = $real_company_id;
                }
            } else {
                //check if the gateway is active with the specified payment mode
    
                $sql = "SELECT * FROM `tbl_payment_gateways` WHERE status = 1 AND priority >= $priority_lower_bound AND priority <= $priority_upper_bound AND payment_mode LIKE '%$pay_mode%' $regionQuery";
    
                $result = $conn->query($sql);
    
                $row_count = $result->num_rows;
    
                if ($row_count) {
                    $segment_counter = $user_unique_id % $row_count;
    
                    while ($row = $result->fetch_assoc()) {
                        if ($segment_counter) {
                            $segment_counter--;
                        } else {
                            $new_pg_choice = $row['id'];
    
                            $new_real_company_id = $row['real_company_id'];
                        }
                    }
                } else {
                    $temp_priority_lower_bound = $priority_lower_bound;
    
                    $temp_priority_upper_bound = $priority_upper_bound;
    
                    $found_gateway = 0;
    
                    while (!$found_gateway) {
                        $temp_priority_lower_bound -= 1000;
    
                        $temp_priority_upper_bound -= 1000;
    
                        if ($temp_priority_lower_bound < 0) {
                            break;
                        }
    
                        $sql = "SELECT * FROM `tbl_payment_gateways` WHERE status = 1 AND priority >= $temp_priority_lower_bound AND priority <= $temp_priority_upper_bound AND payment_mode LIKE '%$pay_mode%' AND real_company_id = '$real_company_id' $regionQuery";
    
                        $result = $conn->query($sql);
    
                        $row_count = $result->num_rows;
    
                        if ($row_count) {
                            $segment_counter = $user_unique_id % $row_count;
    
                            while ($row = $result->fetch_assoc()) {
                                if ($segment_counter) {
                                    $segment_counter--;
                                } else {
                                    $new_pg_choice = $row['id'];
    
                                    $new_real_company_id = $row['real_company_id'];
                                }
                            }
    
                            $found_gateway = 1;
                        } else {
                            $sql = "SELECT * FROM `tbl_payment_gateways` WHERE status = 1 AND priority >= $temp_priority_lower_bound AND priority <= $temp_priority_upper_bound AND payment_mode LIKE '%$pay_mode%' $regionQuery";
    
                            $result = $conn->query($sql);
    
                            $row_count = $result->num_rows;
    
                            if ($row_count) {
                                $segment_counter = $user_unique_id % $row_count;
    
                                while ($row = $result->fetch_assoc()) {
                                    if ($segment_counter) {
                                        $segment_counter--;
                                    } else {
                                        $new_pg_choice = $row['id'];
    
                                        $new_real_company_id =
                                            $row['real_company_id'];
                                    }
                                }
    
                                $found_gateway = 1;
                            }
                        }
                    }
    
                    if (!$found_gateway) {
                        $sql = "SELECT * FROM `tbl_payment_gateways` WHERE id = $pg_choice AND status = 1 $regionQuery";
    
                        $result = $conn->query($sql);
    
                        $row_count = $result->num_rows;
    
                        if ($row_count) {
                            //check if there is a gateway with his preferred company in this list.
    
                            $new_pg_choice = $pg_choice;
                        } else {
                            $new_pg_choice = -2;
                        }
                    }
                }
            }
        }
    
        return $new_pg_choice;
    }

    private function check_disabled_gateway($pg_choice, $user_unique_id, $pay_mode)
    {
        $result_arr = [];

        $result_arr[0] = 0;

        $result_arr[1] = $pg_choice;

        $sql = "SELECT * FROM `tbl_payment_gateways` WHERE id = $pg_choice AND status = 0";

        $result = $conn->query($sql);

        $row_count = $result->num_rows;

        if (!$row_count || $pg_choice == -1 || $pg_choice == -2) {
            return $result_arr;
        }

        $row = $result->fetch_assoc();

        $cur_priority = $row['priority'];

        $sql = "SELECT * FROM `tbl_payment_gateways` WHERE priority = '$cur_priority' AND payment_mode LIKE '%$pay_mode%' AND status = 1";

        $result = $conn->query($sql);

        $row_count = $result->num_rows;

        if ($row_count) {
            $segment_counter = $user_unique_id % $row_count;

            while ($row = $result->fetch_assoc()) {
                if ($segment_counter) {
                    $segment_counter--;
                } else {
                    $result_arr[0] = 1;

                    $result_arr[1] = $row['id'];
                }
            }
        }

        return $result_arr;
    }
}
