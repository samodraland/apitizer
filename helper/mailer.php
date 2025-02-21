<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Mailer{

        public function sendMail($to, $name, $subject, $body, $images = array()){
            $props = Properties::getProperties("email");
            require 'vendor/autoload.php';
            
            $mail = new PHPMailer(true);
            $mail->From = $props["email"];
            $mail->FromName = $props["displayname"];
            $mail->addAddress($to, $name);
            $mail->msgHTML($body);
            $mail->CharSet = "UTF-8";
            $mail->Subject = $subject; 
            $mail->Body = $body;
            if (count($images)){
                foreach($images as $key => $value){
                    $image = end(explode("/",$value["image"]));
                    $mail->addEmbeddedImage(trim($value["image"],"/"), $value["cid"], $image);
                }                
            }
            $mail->IsHTML(true);
            $mail->Hostname = $props["hostname"];
            try {
                $mail->send();
                return json_decode(Utils::response(200),1);
            }catch (Exception $e) {
                $errors = array();
                if (Properties::getProperties("showerror")){
                    $errors = array(
                        "code" => $e->getCode(),
                        "message" => $mail->ErrorInfo
                    );
                }
                return json_decode(Utils::response(500, $errors),1);
            }
        }
    }

?>