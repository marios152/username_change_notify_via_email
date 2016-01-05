<?php
//Change users username and notify him via email.
	class simplehtml_form3 extends moodleform {
		public function definition() {
			global $CFG;
			
			//here I am using my mform to be compatible to my plugin. You need to change it and make it compatible to your plugin.
			
			$mform = $this->_form; // Don't forget the underscore! 
			$mform->addElement('text', 'userida', get_string('userida','your plugin name'));
			$mform->addHelpButton('userida','userida','your plugin name');
			$mform->setType('userida', PARAM_RAW);
			$mform->addElement('text', 'useridu', get_string('useridu','your plugin name'));
			$mform->addHelpButton('useridu','useridu','your plugin name');
			$mform->setType('useridu', PARAM_RAW);

			$radioarray=array();
			$radioarray[] =& $mform->createElement('radio', 'language', '', 'English',0,'');
			$radioarray[] =& $mform->createElement('radio', 'language', '', 'Greek',1,'');
			$mform->setDefault('language', 0); // set the default to english
			$mform->addGroup($radioarray, 'radioar', get_string('language','your plugin name'), array(' '), false);
			$mform->addHelpButton('radioar','language','your plugin name');

			$this->add_action_buttons($cancel = true, $submitlabel=null);
		}	
	}
?>	
<?php
$site="your site";
$token="your token";
//* Find user, change his username and notify him via email
			$mform = new simplehtml_form3();
			echo "<h1>Update users username and notify</h1>";
			if ($mform->is_cancelled()) {
				//Handle form cancel operation, if cancel button is present on form
				$mform->display();
			}else if ($fromform = $mform->get_data()) {
				
				//In this case you process validated data. $mform->get_data() returns data posted in form.
				$mform->display();
				$usera = $fromform->userida;
				$useru = $fromform->useridu;		
				$arrayA = array('username'=>$usera);//get the username
				$resultA = $DB->get_record('user', $arrayA);

					if($resultA && (!empty($useru))){ // if user exists

						$usernameArray= array('username'=>$usera);
						$toUser=$DB->get_record('user', $usernameArray);	 //get info of the user from DB 
						echo "<p>User: ".$toUser->firstname."</p>";

						$URL_Moodle=$site."/webservice/rest/server.php?wstoken=".$token."&wsfunction=core_user_update_users&users[0][id]=".$resultA->id."&users[0][username]=".$useru;		
						
						//open connection
						$ch = curl_init();
						//set the url, number of POST vars, POST data
						curl_setopt($ch,CURLOPT_URL, $URL_Moodle);
						curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // stop curl from printing the result.
						//curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));           //uncomment if it's going to be used in LLL or other platforms behind proxy
						//curl_setopt($ch, CURLOPT_PROXY, "your proxy");					   //uncomment if it's going to be used in LLL or other platforms behind proxy
						//execute post
						$result = curl_exec($ch);
						//close connection
						curl_close($ch);
						//--------------------------------------------------------------------------SEND EMAIL---------------------------------------------------------------------

						$fromUser=$CFG->noreplyaddress;
						
							switch ($fromform->language){
								case "0": //english
									$subject='Moodle Username updated';
									$messageText='';
									$signature ='your signature';
									$messageHtml='<div style="font-family:Arial;">
												Dear '.$toUser->firstname.'<br>
												your username has been changed
												'.$signature.'
												</div>';
												sendEmailChangeUsername($fromUser,$toUser,$subject,$messageHtml);
									break;
								case "1": // greek
									$subject='Το όνομα χρήστη έχει αλλάξει';
									$messageText='';
									$signature ='Η υπογραφή σας';
									$messageHtml='<div style="font-family:Arial;">
												Αγαπητέ/ή '.$toUser->firstname.'<br>
												το όνομα χρήστη έχει αλλάξει
												'.$signature.'
												</div>';
												sendEmailChangeUsername($fromUser,$toUser,$subject,$messageHtml);							
									break;	
							}//end of switch											
					}else{
						echo "<p style='color:red;'>Error with the users you submitted</b></p>";
					}
			} else {
			 
			  //displays the form
			  $mform->display();
			}
		?>	