<?php
    if(!function_exists('_site_title')){
        function _site_title(){
            return 'DJ';
        }
    }

    if(!function_exists('_site_title_sf')){
        function _site_title_sf(){
            return 'DJ';
        }
    }

    if(!function_exists('_mail_from')){
        function _mail_from(){
            return 'info@cypherocean.com';
        }
    }

    if(!function_exists('_firebase_notification')){
        function _firebase_notification($fcm_token, $title = '', $body = ''){
            $url = "https://fcm.googleapis.com/fcm/send";
            
            $token = $fcm_token;
            
            $serverKey = 'AAAAVb8dwyQ:APA91bFvX1cDg7ru3MP4a-zAJzm0HSvmDEiNP9QxJ74QJRuj4lc5oYAiBgm47-iVhpS_FJ_T9YZlO2WHUwCdevoCYXAgkP6jTDAjiOH46rRHKDH2Ww8Cat_hwy-Pr0kPbF-FT-ltT8YA';
            
            if($title == '')
                $title = "Notification title";
            
            if($body == '')
                $body = "Hello I am from Your php server";
            
            $notification = array('title' => $title, 'body' => $body, 'sound' => 'default', 'badge' => '1');
            
            $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high');
            
            $json = json_encode($arrayToSend);
            
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);
   
            if ($response === FALSE)
                return false;
            else
                return false; 
        }
    }

    if(!function_exists('_generate_qrcode')){
        function _generate_qrcode($id, $folder){
            if($id == '')
                return false;
            
            if($folder == 'item'){
                $folder_to_uploads = public_path().'/uploads/qrcodes/items/';
                $exst_file = public_path().'/uploads/qrcodes/items/qrcode_'.$id.'.png';
                $table = 'items';
                $qrcode = 'item-'.$id;
            }elseif($folder == 'item_inventory'){
                $folder_to_uploads = public_path().'/uploads/qrcodes/items_inventory/';
                $exst_file = public_path().'/uploads/qrcodes/items_inventory/qrcode_'.$id.'.png';
                $table = 'items_inventories';
                $qrcode = 'itemInventories-'.$id;
            }elseif($folder == 'sub_item'){
                $folder_to_uploads = public_path().'/uploads/qrcodes/sub_items/';
                $exst_file = public_path().'/uploads/qrcodes/sub_items/qrcode_'.$id.'.png';
                $table = 'sub_items';
                $qrcode = 'subItem-'.$id;
            }elseif($folder == 'sub_item_inventory'){
                $folder_to_uploads = public_path().'/uploads/qrcodes/sub_items_inventory/';
                $exst_file = public_path().'/uploads/qrcodes/sub_items_inventory/qrcode_'.$id.'.png';
                $table = 'sub_items_inventories';
                $qrcode = 'subItemInventories-'.$id;
            }

            if (!File::exists($folder_to_uploads))
                File::makeDirectory($folder_to_uploads, 0777, true, true);

            if(File::exists($exst_file) && $exst_file != '')
                @unlink($exst_file);
            
            $qrname = 'qrcode_'.$id.'.png';

            QrCode::size(500)->format('png')->merge('/public/qr_logo.png', .3)->generate($qrcode, $folder_to_uploads.$qrname);

            $update = DB::table($table)->where(['id' => $id])->update(['qrcode' => $qrname, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => auth()->user()->id]);
            
            if($update)
                return true;
            else
                return false;
        }
    }

    if(!function_exists('_qrcode')){
        function _qrcode($input){
            if(empty($input))
                return false;

            $input = explode('-', $input);
 
            if(empty($input[0]))
                return false;
            else
                $folder = $input[0];

            if(empty($input[1]))
                return false;
            else
                $id = $input[1];

            if($folder == 'item')
                $table = 'items';
            elseif($folder == 'itemInventories')
                $table = 'items_inventories';
            elseif($folder == 'subItem')
                $table = 'sub_items';
            elseif($folder == 'subItemInventories')
                $table = 'sub_items_inventories';
            else
                return false;

            $data = DB::table($table)->where(['id' => $id])->first();
            
            if($data)
                return true;
            else
                return false;
        }
    }
?>