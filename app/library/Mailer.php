<?php

class Mailer extends Phalcon\Mvc\User\Component
{

	public $dataSend;

	public function __construct(){
		$config = include __DIR__ . "/../config/config.php";
		require_once __DIR__ . "/PHPMailer/class.phpmailer.php";
		require_once __DIR__ . "/PHPMailer/class.smtp.php";

		$this->dataSend = array(
			"IsHTML" => true,
			"CharSet" => "UTF-8",
			"Host" => $config->mail->smtp->server,
			"SMTPAuth" => "true",
			"Username" => $config->mail->smtp->username,
			"Password" => $config->mail->smtp->password,
			"From" => $config->mail->fromEmail,
			"FromName" => $config->mail->fromName,
			"Subject" => "",
			"Html" => "",
			"mails" => array(),
			"files" => array(),

		);
	}




	public function Send(){



 		$mail = new PHPMailer();
        $mail->IsHTML($this->dataSend["IsHTML"]);
        $mail->CharSet = $this->dataSend["CharSet"];
        $mail->IsSMTP();
        $mail->Host = $this->dataSend["Host"];
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->Username = $this->dataSend["Username"];
        $mail->Password = $this->dataSend["Password"];
        $mail->From = $this->dataSend["From"];
        $mail->FromName = $this->dataSend["FromName"];
        $mail->Subject = $this->dataSend["Subject"];
        $mail->msgHTML($this->dataSend["Html"]);
        foreach($this->dataSend["files"] as $value){
        	$mail->addAttachment($value);
        }
        foreach($this->dataSend["mails"] as $value){
        	$mail->addAddress($value , "");
        }
        if($mail->send()){
        	return true;
        } else{ 
        	echo $mail->ErrorInfo;
        	return false;
        }
	}



}



