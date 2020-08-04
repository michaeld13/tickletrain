<div class="main_holder register_area">
    <div><?php echo $Form->ErrorString . $Form->ErrSufix; ?></div>
    <h1><span>Sign up</span> and put your emails to work</h1>
    <div class="form">
        <div class="holder">
            <div class="frame">
                <form action="<?= Url_Create('Register') ?>" method="post" id="Register">
                    <fieldset>
                        <div class="row">
                            <label for="Username">Username <span class="req">*</span></label>
                            <span class="input_text"><input type="text" name="Username" id="Username" value="<?= trim($_POST['Username']) ?>"/></span>
                        </div>
                        <div class="row">
                            <label for="Password">Password <span class="req">*</span></label>
                            <span class="input_text"><input type="password" name="Password" id="Password"  value="<?= trim($_POST['Password']) ?>" /></span>
                        </div>
                        <div class="row">
                            <label for="RPassword">Repeat Password <span class="req">*</span></label>
                            <span class="input_text"><input type="password" name="RPassword" id="RPassword"  value="<?= trim($_POST['RPassword']) ?>" /></span>
                        </div>
                        <div class="row">
                            <label for="EmailID">E-mail <span class="req">*</span></label>
                            <span class="input_text"><input type="text" name="EmailID" id="EmailID"  value="<?= trim($_POST['EmailID']) ?>"/></span>
                        </div>
                        <div class="row">
                            <label for="REmailID">Repeat E-mail <span class="req">*</span></label>
                            <span class="input_text"><input type="text" name="REmailID" id="REmailID"  value="<?= trim($_POST['REmailID']) ?>"/></span>
                        </div>
                        
                        <div class="row">
                            <label for="Plan">Plan<span class="req">*</span></label>
                            <select name="Plan" id="Plan">
                               <? $plans= whmcs_getPlans(); 
                               foreach($plans as $plan){
                                   if($plan['pid']==$_POST['Plan']){
                                       $selected="selected='selected'";
                                   }else{
                                       $selected='';
                                   }
                                   echo '<option value="'.$plan['pid'].'" '.$selected.'>'.$plan['name'].'</option>';
                               }
                               
                               ?>
                            </select>
                        </div>
                        <div class="row">
                            <label for="Timezone">Time zone <span class="req">*</span></label>
                            <select name="Timezone" id="Timezone">
                                <? foreach ($timezones as $tm => $zone): ?>
                                    <option value="<?= $tm ?>"<?= checkSelection($tm, (trim($_POST['Timezone']) ? trim($_POST['Timezone']) : "America/Chicago")) ?>><?= $zone ?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                        <div class="row">
                            <label for="FirstName">First Name <span class="req">*</span></label>
                            <span class="input_text"><input type="text" name="FirstName" id="FirstName" value="<?= trim($_POST['FirstName']) ?>"/></span>
                        </div>
                        <div class="row">
                            <label for="LastName">Last Name <span class="req">*</span></label>
                            <span class="input_text"><input type="text" name="LastName" id="LastName"  value="<?= trim($_POST['LastName']) ?>"/></span>
                        </div>
                        <div class="submit_holder">
                            <input type="submit" value="Register" class="btn_register" name="submit" />
                            <p>Already registered? <a href="#" onclick="return SendActivation();">Click here</a> to get activation e-mail or restore your password.</p>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <p class="terms">By registering and using TickleTrain, you are accepting our <a href="#terms" class="show_lb">Terms of Use</a>.</p>

</div>
<div style="display: none;">
    <div class="terms_txt" id="terms">
        <h3>TERMS OF USE</h3>
        <p>TickleTrain is an email follow-up service, herein after called "Service."</p>
        <p>You must be at least thirteen (13) years of age to use this Service. You must provide current, accurate identification, contact, and other information that may be required as part of the registration process and/or continued use of the Service.  You agree that you are responsible for your own communications and for any consequences thereof.  You agree that you will use the Service in compliance with all applicable local, state, national, and international laws, rules and regulations, including any laws regarding the transmission of technical data exported from your country of residence.</p>
        <p>You agree to hold harmless and indemnify TickleTrain, and its subsidiaries, affiliates, officers, agents, and employees from and against any third party claim arising from or in any way related to your use of the Service, including any liability or expense arising from all claims, losses, damages (actual and consequential), suits, judgments, litigation costs and attorneys’ fees, of every kind and nature. In such a case, TickleTrain will provide you with written notice of such claim, suit or action.</p>
        <p>You may not sell, assign, grant a security interest in or otherwise transfer any right in the Service or incorporate it (or any portion of it) into another product. You may not copy the Service. You may not translate, reverse-engineer or reverse-compile or decompile, disassemble, make derivative works from, or otherwise attempt to discover any source code in the Service. You may not modify the Service or use it in any way not expressly authorized by these Terms of Use. You agree not to alter, duplicate, modify, adapt, rent, lease, loan, sublicense, copy, reproduce, create derivative works from, distribute or provide others with the Service in whole or part, merge the Service into or add it to other software or program material to form an updated work or otherwise, or transmit or communicate the Service over a network. Without limiting the generality of the foregoing, you shall not create or ascertain or attempt to create or ascertain, by reverse assembling, reverse engineering, disassembling or reverse compiling or otherwise, any components of the Service (including, without limitation, the source code of the Service or any part thereof) from the object code or from other information made available under this Terms of Use or otherwise attempt to discover the source code of the Service. Finally, you may not authorize or assist any third party to do any of the things described in this paragraph.</p>
        <p>After a period of inactivity, whereby a user fails to login to an account for a period of nine months, Service reserves the right to disable or terminate the account. If an account has been deactivated for inactivity, the username associated with that account may be given to another user without notice to you or such other party.</p>
        <p>You may not use Service to send unsolicited bulk communications, including through e-mail. Similarly, you may not authorize others to use your account to send unsolicited bulk communications, or cause unsolicited bulk communications to be sent by someone else. Any violation of these provisions may result in immediate termination of your account and further legal action. You agree that Service may take any legal and technical remedies to prevent unsolicited bulk communications.</p>
        <p>Service reserves the right to run advertisements and promotions on the Service website. By accepting the terms of this License, you agree that we have the right to run such advertisements and promotions without compensation to you. The timing, frequency, placement and extent of advertising by us within the pages comprising the Service is subject to change and shall be determined by us at our sole discretion. Your correspondence or business dealings with, or participation in promotions of, advertisers found on or through the Service web site, including payment and delivery of related goods or services, and any other terms, conditions, warranties or representations associated with such dealings, are solely between you and such advertiser and shall not impose any responsibility of any kind on Service. You agree that Service is not be responsible or liable for any loss or damage of any sort incurred as the result of any such dealings or as the result of the presence of such advertisers on the Service web site.</p>
        <p>As between Service and you, Service is the sole owner of the Software, including without limitation, all applicable U.S. and non-U.S. copyrights, patents, trademarks, and trade secrets, and other intellectual property rights thereto. All title and intellectual property rights in and to the content of any third party web site which may be linked to or viewed in connection with the service is the property of the respective content owner and may be protected by applicable copyright or other intellectual property laws and treaties. This Agreement grants you no rights to use such content.</p>
        <p> Any unsolicited materials submitted or sent to Service will be deemed to be not confidential or secret. By submitting or sending information or other material to Service you:
            (a) Warrant that you have all rights of any kind to the material and that to the best of your knowledge no other party has any rights to the material; and
            (b) grant Service an unrestricted, perpetual, irrevocable license to use, reproduce, display, perform, modify, transmit and distribute the material, and you further agree that Service is free to use any ideas, know-how, concepts or techniques you send us for any purpose, without any compensation to you or any other person.</p>
        <p>Recognizing the global nature of the Internet, you agree to comply with all local rules regarding on-line conduct and privacy. Specifically, you agree to comply with all applicable laws regarding privacy and privacy invasion which apply in the country in which you reside</p>
        <h4>Electronic Delivery Policy</h4>
        <p>Service, as an online business, transacts with its users electronically. You agree that Service generally can send you electronic Notices in either or both of the following ways: (1) to the e-mail address that you provided to Service during registration or (2) on a welcoming screen or top page of the relevant Service. The delivery of any Notice from Service is effective when sent by Service, regardless of whether you read the Notice when you receive it or whether you actually receive the delivery. Your only method of terminating receipt of Notices electronically is to cancel your registration and terminate all subscriptions, services or other products provided under these Terms of Service.</p>
    </div>
</div>
<script language="javascript">
    function SendActivation(){
        var cancel = {text:'Cancel', click: function() {$(this).dialog('close')}};
        var send = {text:'Send', click: function() {
                $(this).dialog('close');

                $.get("<?= Url_Create('Register') ?>", {'act':'activationemail', 'email': $("#actemail").val()} , function(data){
                    if (data=="activation"){
                        var msg='<center>You TickleTrain activation email has been resent to '+$("#actemail").val()+'! <br/><br/>Please, also check with your junk and or spam folders as the email may have been filtered into those folders depending on your spam settings. If you still do not receive the email kindly check with your email provider for more details.</center>';
                        mcalert('Activation Email Sent', msg,{width:500});
                    }
                    if (data=="restore"){
                        var msg='<center>Your password has been resent to your email address.<br/><br/>Make sure to check your SPAM folder and add "noreply@tickletrain.com" to your safe list</center>';
                        mcalert('Password Sent',msg,{width:500});
                    }
                    if (data=="false"){
                        mcalert('Account Not Found',"We're sorry but we do not have an account with that email address. Please verify your email address and try again or re-register. If you are still experiencing problems, please contact <a href='mailto:sales@tickletrain.com'>sales@tickletrain.com</a>. We apologize for any inconvenience.");
                    }
                });
            }
        };
        var message = "Enter your email address: <input type='text' name='actemail' id='actemail'/>";
        mdialog("Restore Password",message,[send,cancel]);
    }
</script>