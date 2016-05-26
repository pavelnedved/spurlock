$(document).ready(function() {
     $('input[type="submit"]').attr('disabled','disabled');
     $('input[type="text"]').keyup(function() {
        if($(this).val() != '' ) {
           $('input[type="submit"]').removeAttr('disabled');
        }
     });
 });
$(function() {
    $('#ShareInfoForm').on('submit', function(e) {
    	var x=document.forms["ShareInfoForm"]["ShareInfoemail"].value;
    	var atpos=x.indexOf("@");
    	var dotpos=x.lastIndexOf(".");
    	var y=document.forms["ShareInfoForm"]["ShareInfofirst_name"].value;
    	var z=document.forms["ShareInfoForm"]["ShareInfolast_name"].value;
    	var w=document.forms["ShareInfoForm"]["ShareInfocomments"].value;
    	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
    	  {
    	  $('#validation').html('&nbsp;&nbsp;&nbsp;Invalid E-mail Address.');
    	  return false;
    	  }
    	else if (y==null || y=="" || z==null || z=="" || w==null || w=="") {
    		$('#validation').html('&nbsp;&nbsp;&nbsp;Some fields have been left blank.');
    		return false;
    	}
    	$("#validation").empty();
    	$("#msgsubmitted").show(100);
    	$("#sendingmessageajaxloader").delay(100).show(100);
    		$("#submit").hide(100);
        	$("#reset").hide(100);
        $.post('PHPMailer/send_form_email.php', $(this).serialize(), function (data) {
            // This is executed when the call to mail.php was succesful.
            // 'data' contains the response from the request
        	$("#sendingmessageajaxloader").delay(1600).hide(400);           	
           	$('#thanks').delay(2000).replaceWith('Thanks for sharing!  Check the email address you provided for a copy of your submission.');
           	$("#ShareInfoForm").delay(1600).hide(400);
        }).error(function() {
        	$("#sendingmessageajaxloader").delay(2000).hide(400);
           	$('#thanks').delay(3000).replaceWith('An error occurred...Message not sent.  Please try again later.');// This is executed when the call to mail.php failed.
        });
        e.preventDefault();
    });
});