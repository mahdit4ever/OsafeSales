var loginCallback;
var loggedIn = false;
var userID;

var pushNotification;
var RegID;
var deviceID;
var messagePage;

/* Notification */

document.addEventListener("deviceready", function(){
	pushNotification = window.plugins.pushNotification;
    
	if ( device.platform == 'android' || device.platform == 'Android'){
		pushNotification.register(
		successHandler,
		errorHandler,
		{
			"senderID":"52365757768",
			"ecb":"onNotification"
		});
	} else {
		pushNotification.register(
		tokenHandler,
		errorHandler,
		{
			"badge":"true",
			"sound":"true",
			"alert":"true",
			"ecb":"onNotificationAPN"
		});
	}
	
	function successHandler (result) {
		//alert('result = ' + result);
	}
	
	function errorHandler (error) {
		//alert('error = ' + error);
	}
	
	function tokenHandler (result) {
		// Your iOS push server needs to know the token before it can push to this device
		// here is where you might want to send it the token for later use.
		//alert('device token = ' + result);
		RegID = result;
	}

});

function onNotificationAPN (event) {
	if ( event.alert )
	{
		navigator.notification.alert(event.alert);
	}

	if ( event.sound )
	{
		var snd = new Media(event.sound);
		snd.play();
	}

	if ( event.badge )
	{
		pushNotification.setApplicationIconBadgeNumber(successHandler, errorHandler, event.badge);
	}
}

function onNotification(e) {
	switch( e.event )
	{
	case 'registered':
		if ( e.regid.length > 0 )
		{
			//console.log("regID = " + e.regid);
			//alert("regID = " + e.regid);
			RegID = e.regid;
		}
	break;

	case 'message':
		if ( e.foreground )
		{
			
		}
		else
		{  // otherwise we were launched because the user touched a notification in the notification tray.
			if ( e.coldstart )
			{	
				//alert(e.payload.message);
				var temp = e.payload.message;
				if(temp.indexOf("message") > -1){
					messagePage = "message";
				} 
			}
			else
			{
				//alert(e.payload.message);
			}
		}
	break;

	case 'error':
		
	break;

	default:
		
	break;
  }
}

function updateDeviceId(){
	$.ajax(
	{
		url: 'http://mobionfire.com/savenow_api/register_device.php',
		method: 'post',
		cache: false,
		data:
		{
			user: uid,
			device: deviceID
		}
	})
	.done(function(response){
				  
	})
	.fail(function(){
	});
}


function isLogin(){
	if(!loggedIn){
		navigator.notification.alert('Please log in to your account first.', function()
		{
			pushPage('profile', LTR);
		}, 'User not found');
		return false;
	} else return true;
}

/* Login with username */

function login(username, password)
{
//alert(username);
	$('#login').addClass('loading');
	$('#login .content').hide();
	
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/login.php',
		method: 'get',
		cache: false,
		data:
		{
			email: username,
			password: password
		}
		})
	    .done(function(response){
		//alert(response);
			var json = $.parseJSON(response);
			
			alert(response);
			if(json.length > 1 && json[1].status == 'Active')
			{
				loggedIn = true;
				//navigator.notification.alert('Login', null, 'Successful');
				window.localStorage.setItem("key", username);
				window.localStorage.setItem("key2", password);
				
				deviceID = json[1].deviceid;
				window.uid= json[1].index;
				window.myphone = json[1].mobile;
				window.myemail = json[1].email;
				window.myage = json[1].age;
				

				deviceID = RegID;
				updateDeviceId();
				
				var html = '<div class="item clearfix"><div style="border-bottom: 2px solid #eee; padding-bottom: 20px;"><img class="avatar" id="profile_pic" onClick="profile_picChange();playMP3(); vibrate();" src="http://sunday-tech.com/osafe/upload/' + json[1].photo_url + '"></div>';
				
				html += '<div><table width="100%">';
				
				html += '<td style="width:15%; border:none"><div class="bio3" style><font size="5"><i class="icon ion-android-person"></i></font></div></td><td ><b>Name</b></td><td style="text-align:right; width:60%;">' + json[1].name + '</td></tr>';
				
				//html += '<tr><td style="border:none"><div class="bio3" style><font size="5"><i class="icon ion-male"></i></font></div></td><td><b>Gender</b></td><td style="text-align:right">' + json[1].gender + '</td></tr>';
				
				html += '<tr><td style="border:none"><div class="bio3" style><font size="5"><i class="icon ion-ios-email"></i></font></div></td><td><b>Email</b></td><td style="text-align:right">' + json[1].email + '</td></tr> ';
				
				html += '<tr><td><div class="bio3" style><font size="5"><i class="icon ion-android-call"></i></font></div></td><td><b>Mobile</b></td><td style="text-align:right">' + json[1].mobile + '</td></tr>';
				
				//html +='<tr><td><div class="bio3" style><font size="5"><i class="icon ion-ios-star-half"></i></font></div></td><td><b>Point</b></td><td style="text-align:right">' + json[1].point + '</td></tr>';
				
				html +='</table></div></div>';
				
				//html += '<a class="button col-2 mb-18" onClick="pushPage(\'inbox\', RTL);loadinbox();playMP3(); vibrate();"><i style="float:left" class="icon ion-chatbox-working"></i> Inbox</a>';
				
				html += '<a class="button col-2 mb-18" onClick="profile_edit();playMP3(); vibrate();"><i style="float:left" class="icon ion-compose"></i> Edit Profile</a>';
				
				html += '<a class="button col-5 mb-18" onClick="onLogout();playMP3(); vibrate();"><i style="float:left" class="icon ion-log-out"></i> Logout</a>';
				
				html += '<a class="button fit mb-18" style="background:#28272b;" onClick="pushPage(\'change_password\', RTL);playMP3(); vibrate();"><i style="float:left" class="icon ion-locked"></i> Change Password</a>';
				
				$('#profile .content').html(html);
				
				$('#login input').val('');
				if(loginCallback)
				{
					loginCallback();
					loginCallback = null;
				}
			}
			else
			{
				alert("Wrong username or password");
			}
			$('#login').removeClass('loading');
			$('#login .content').show();
		})
		.fail(function(){
		alert("No internet connection");
		$('#login').removeClass('loading');
		$('#login .content').show();
	});
}

/* Auto Login */

function autoLogin(username, password)
{
	$('#login').addClass('loading');
	$('#login .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/login.php',
		method: 'get',
		cache: false,
		data:
		{
			email: username,
			password: password
		}
		})
	    .done(function(response){
			var json = $.parseJSON(response);
			if(json.length > 1 && json[1].status == 'Active')
			{
				loggedIn = true;
				
				window.localStorage.setItem("key", username);
				window.localStorage.setItem("key2", password);
				
				window.uid= json[1].index;
				window.myphone = json[1].mobile;
				window.myemail = json[1].email;
				window.myage = json[1].age;
				

				var html = '<div class="item clearfix"><div style="border-bottom: 2px solid #eee; padding-bottom: 20px;"><img class="avatar" id="profile_pic" onClick="profile_picChange();playMP3(); vibrate();" src="http://sunday-tech.com/osafe/upload/' + json[1].photo_url + '"></div>';
				
				html += '<div><table width="100%">';
				
				html += '<td style="width:15%; border:none"><div class="bio3" style><font size="5"><i class="icon ion-android-person"></i></font></div></td><td ><b>Name</b></td><td style="text-align:right; width:60%;">' + json[1].name + '</td></tr>';
				
				//html += '<tr><td style="border:none"><div class="bio3" style><font size="5"><i class="icon ion-male"></i></font></div></td><td><b>Gender</b></td><td style="text-align:right">' + json[1].gender + '</td></tr>';
				
				html += '<tr><td style="border:none"><div class="bio3" style><font size="5"><i class="icon ion-ios-email"></i></font></div></td><td><b>Email</b></td><td style="text-align:right">' + json[1].email + '</td></tr> ';
				
				html += '<tr><td><div class="bio3" style><font size="5"><i class="icon ion-android-call"></i></font></div></td><td><b>Mobile</b></td><td style="text-align:right">' + json[1].mobile + '</td></tr>';
				
				//html +='<tr><td><div class="bio3" style><font size="5"><i class="icon ion-ios-star-half"></i></font></div></td><td><b>Point</b></td><td style="text-align:right">' + json[1].point + '</td></tr>';
				
				html +='</table></div></div>';
				
				//html += '<a class="button col-2 mb-18" onClick="pushPage(\'inbox\', RTL);loadinbox();playMP3(); vibrate();"><i style="float:left" class="icon ion-chatbox-working"></i> Inbox</a>';
				
				html += '<a class="button col-2 mb-18" onClick="profile_edit();playMP3(); vibrate();"><i style="float:left" class="icon ion-compose"></i> Edit Profile</a>';
				
				html += '<a class="button col-5 mb-18" onClick="onLogout();playMP3(); vibrate();"><i style="float:left" class="icon ion-log-out"></i> Logout</a>';
				
				html += '<a class="button fit mb-18" style="background:#28272b;" onClick="pushPage(\'change_password\', RTL);playMP3(); vibrate();"><i style="float:left" class="icon ion-locked"></i> Change Password</a>';
				
				$('#profile .content').html(html);
				$('#login input').val('');
			}
			$('#login').removeClass('loading');
			$('#login .content').show();
		
				if(messagePage == "message"){
					setTimeout(pm_checkPage, 3000);
					//pm_checkPage();
				} 
		})
		.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			popPage();
		}, 'No internet connection');
		$('#login').removeClass('loading');
		$('#login .content').show();
	});
}

/* Register */

function register(fullName, username, password, mobile, email, dob, gender)
{
	$('#register').addClass('loading');
	$('#register .content').hide();
	$.ajax(
	{
		url: '',
		method: 'get',
		cache: false,
		data:
		{
			name: fullName,
			username: username,
			password: password,
			mobile: mobile,
			email: email,
			birthday: dob,
			gender: gender
		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#register input').val('');
			$('#register #gender').val('Male');
			navigator.notification.alert('Your account has been successfully registered. It is now pending for admin approval.', function()
			{
				pushPage('login', LTR);
			}, 'Successful');
		}
		else if($.trim(response) == '1')
		{
			navigator.notification.alert('Your username has already been registered. Please login or try another username.', null, 'Error');
		}
		else
		{
			navigator.notification.alert('Your email has already been registered. Please login or try another email.', null, 'Error');
		}
		$('#register').removeClass('loading');
		$('#register .content').show();
	})
	.fail(function()
	{
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#register').removeClass('loading');
		$('#register .content').show();
	});
}


/* Change Password */

function password_change(){
	var oldPassword = $.trim($('#change_password #password-old').val());
	var password = $.trim($('#change_password #password').val());
	var password2 = $.trim($('#change_password #password2').val());
	
	if(oldPassword != window.localStorage.getItem("key2"))
	{
		navigator.notification.alert('Your old password is incorrect!', function()
		{
			$('#change_password #password-old').focus();
		}, 'Error');
		return;
	}
	if(password == '' || password.length < 6)
	{
		navigator.notification.alert('Your password must have at least 6 characters', function()
		{
			$('#change_password #password').focus();
		}, 'Error');
		return;
	}
	if(password2 != password)
	{
		navigator.notification.alert('Password does not match', function()
		{
			$('#change_password #password2').focus();
		}, 'Error');
		return;
	}
	
	$('#change_password').addClass('loading');
	$('#change_password .content').hide();
	
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/password_change.php',
		method: 'post',
		cache: false,
		data:
		{
			user: uid,
			password: password
		}
	})
	.done(function(response){
		if(response != '')
		{
			navigator.notification.alert('You have successfully changed your password, please login using your new password', function()
			{
				
			}, 'Successful');
		}
		$('#change_password input').val('');
		logout();
		$('#change_password').removeClass('loading');
		$('#change_password .content').show();
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#change_password').removeClass('loading');
		$('#change_password .content').show();
	});
}

function profile_edit(){
	pushPage('edit_profile');
	$('#edit_profile #mobile').val(window.myphone);
	$('#edit_profile #email').val(window.myemail);
}

function profile_update(){
	var mobile = $.trim($('#edit_profile #mobile').val());
	var email = $.trim($('#edit_profile #email').val());
	
	if(!isPhoneNumber(mobile))
	{
		navigator.notification.alert('Please enter your valid mobile number in the format of 60171234567', function()
		{
			$('#edit_profile #mobile').focus();
		}, 'Error');
		return;
	}
	if(!isEmailAddress(email))
	{
		navigator.notification.alert('Please enter your valid email address', function()
		{
			$('#edit_profile #email').focus();
		}, 'Error');
		return;
	}
	
	$('#edit_profile').addClass('loading');
	$('#edit_profile .content').hide();
	
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/profile_update.php',
		method: 'post',
		cache: false,
		data:
		{
			user: uid,
			phone: mobile,
			email: email
		}
	})
	.done(function(response){
		if(response != '')
		{
			navigator.notification.alert('You have successfully updated your profile', function()
			{
				popPage();
			}, 'Successful');
		}
		var username = window.localStorage.getItem("key");
		var password = window.localStorage.getItem("key2");
		login(username, password);
		$('#edit_profile').removeClass('loading');
		$('#edit_profile .content').show();
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#edit_profile').removeClass('loading');
		$('#edit_profile .content').show();
	});
}


/* Logout */

function logout()
{
	//window.localStorage.clear();
	localStorage.removeItem("key");
	localStorage.removeItem("key2");
	clearUserInfo();
	loggedIn = false;
}

function profile_picChange(){
	navigator.notification.confirm
	(
		'Do you want to capture a photo or upload a photo?',
		function(buttonIndex)
		{
			if(buttonIndex == 1) profile_postAddPic();
			if(buttonIndex == 2) profile_postGetPhoto();
		},
		'Photo Option',
		['Capture', 'Upload']
	);
}

function profile_postAddPic() {
	
    navigator.camera.getPicture (profile_cameraSuccess, profile_cameraFail, 
    { quality: 30,  
      sourceType: navigator.camera.PictureSourceType.CAMERA,
      destinationType: navigator.camera.DestinationType.FILE_URI,
      encodingType: navigator.camera.EncodingType.PNG,
	  targetWidth: 800,
      correctOrientation: true
      //saveToPhotoAlbum: true
	});

    // A callback function when snapping picture is success.
    function profile_cameraSuccess (imageData) {
        profile_updatePic(imageURI);
    }

    // A callback function when snapping picture is fail.
    function profile_cameraFail (message) {
        //alert ('Error occured: ' + message);
    }
}

function profile_postGetPhoto() {
      // Retrieve image file location from specified source
	navigator.camera.getPicture(profile_onPhotoURISuccess, profile_onPhotoURIFail, 
	{ quality: 30,
      destinationType: navigator.camera.DestinationType.FILE_URI,
	  encodingType: navigator.camera.EncodingType.PNG,
	  targetWidth: 800,
      correctOrientation: true,
      sourceType: navigator.camera.PictureSourceType.PHOTOLIBRARY });
		
	function profile_onPhotoURISuccess(imageURI) {
		profile_updatePic(imageURI);
	}
	
	function profile_onPhotoURIFail(message) {
		//alert('Failed because: ' + message);
	}
}


function profile_updatePic(src)
{
	var imageURI = src;
	
	$('#profile').addClass('loading');
	$('#profile .content').hide();
	
	var date_time = new Date();
	
    //set upload options
	var options = new FileUploadOptions();
        options.fileKey="file";
        options.fileName=userID + "_" + date_time.getFullYear() + "-" + date_time.getMonth() + "-" + date_time.getDate() + "_" + date_time.getHours() + "-" + date_time.getMinutes() + "-" + date_time.getSeconds() + ".jpg";
        options.mimeType="image/jpeg";
        
    var params = new Object();
        params.value1 = userID;
 
        options.params = params;
        options.chunkedMode = false;
      
    var ft = new FileTransfer();
        ft.upload(imageURI, encodeURI ("http://sunday-tech.com/resident/api/profile_pic.php"), win, fail, options);
		
	function win(r) {
		$('#profile').removeClass('loading');
		$('#profile .content').show();
		navigator.notification.alert('Your profile picture has been updated.', null, 'Successful');
		var libraryImage = document.getElementById('profile_pic');
		libraryImage.src = imageURI;
    }

    function fail(error) {
		$('#profile').removeClass('loading');
		$('#profile .content').show();
        navigator.notification.alert('An error has occurred, please try again later.', null, 'Error');
	}	
}

function password_retrieve()
{
	var username = $.trim($('#retrieve_password #username').val());

	if(username == '')
	{
		navigator.notification.alert('Please enter your username.', function()
		{
			$('#retrieve_password #username').focus();
		}, 'Missing info');
		return;
	}
	
	$('#retrieve_password').addClass('loading');
	$('#retrieve_password .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/retrieve_password.php',
		method: 'post',
		cache: false,
		data:
		{
			email: username,
		}
	})
	.done(function(response){
		var json = $.parseJSON(response);
		var password;
		var mobile;
		if(json.length > 1){
			password = json[1].password;
			mobile = json[1].mobile;
			password_sendSMS(mobile, password);
			navigator.notification.alert('Your password has been sent to your phone.', null, 'Successful');
			$('#retrieve_password').removeClass('loading');
			$('#retrieve_password .content').show();
			popPage();
		}
		else {
			navigator.notification.alert('Username not found, please try again or contact admin for username retrieval.', null, 'Error');
			$('#retrieve_password').removeClass('loading');
			$('#retrieve_password .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#retrieve_password').removeClass('loading');
		$('#retrieve_password .content').show();
	});
}

function password_sendSMS(mobile, password)
{
	var content = 'Your password for Iremi SRMS mobile application account is ' + password;
	content = encodeURI(content);
	
	$.ajax(
	{
		url: 'http://sunday-tech.com/iteksi/api/server_receiver.php',
		method: 'get',
		cache: false,
		data:
		{
			port: 'COM4',
			type: '1',
			msisdn: mobile,
			content: content
		}
	})
	.done(function(response){
		
	})
	.fail(function(){
		
	});
}


/* Show login, register buttons */

function clearUserInfo()
{
	var html = '<div class="mb-18">You are not logged in.</div>';	
	html += '<a class="button fit mb-18" onClick="pushPage(\'login\', RTL);playMP3(); vibrate();"><i style="float:left;" class="icon ion-log-in"></i> Login</a>';

	$('#profile .content').html(html);
}
   
/* -------------------------------------------------- Sound -------------------------------------------------- */ 
/*
document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {
	document.querySelector("#playMp3").addEventListener("touchend", playMP3, false);
};

function playMP3() {
    var mp3URL = getMediaURL("assets/sounds/Effect_Tick.ogg");
    var media = new Media(mp3URL, null, mediaError);
    media.play();
}

function getMediaURL(s) {
    if(device.platform.toLowerCase() === "android") return "/android_asset/www/" + s;
    return s;
}

function mediaError(e) {
    alert('Media Error' +e );
    alert(JSON.stringify(e));
} */

function playMP3()
{
       var audio = document.getElementById("audio");
       audio.play();
}

/* -------------------------------------------------- Vibrate -------------------------------------------------- */

function vibrate() {
	navigator.notification.vibrate(50);
	}

////////////////////////////////////////////////////////////////////////////////////////
/* Leave Functions */
////////////////////////////////////////////////////////////////////////////////////////

var leaveEntry;
var leaveCurrentPage = 1;
var leaveJson;

function leave_login(){
	if(isLogin()){
		pushPage('leave', RTL); 
	}
}

function leave_checkPage()
{
	$('#leave').addClass('loading');
	$('#leave .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/leave_counter.php',
		method: 'post',
		cache: false,
		data:
		{
			user: uid
		}
	})
	.done(function(response)
	{
		var json = $.parseJSON(response);
		if(json[1].total > 0)
		{
			leaverEntry = json[1].total;
		}
		
		leave_load();
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			popPage();
		}, 'No internet connection');
		$('#leave').removeClass('loading');
		$('#leave .content').show();
	});
}


function leave_load()
{	
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/leave_list.php',
		method: 'post',
		cache: false,
		data:
		{
			page: leaveCurrentPage,
			user: uid
		}
	})
	.done(function(response){
		leaveJson = $.parseJSON(response);
		var html = '';
		if(leaveJson.length > 1){
			for(var i = 1; i < leaveJson.length; i++){
				if(leaveJson[i].status == "Pending")
				{
				html += '<div class="item clearfix" onClick="leave_onDetails(' + i + '); pushPage(' + "'leave-detail'" + ', RTL); playMP3();vibrate();">';
				html += '<div class="item-desc mt-10"><div class="title">' + leaveJson[i].date_from + ' To ' + leaveJson[i].date_to + '</div><div class="date"></div><div class="mt-10"><b>Status:</b> ' + leaveJson[i].status +'</div></div><div class="item-info1"></div></div>';
				}
				else if(leaveJson[i].status == "Approved")
				{
				html += '<div class="item clearfix" onClick="leave_onDetails(' + i + '); pushPage(' + "'leave-detail'" + ', RTL); playMP3();vibrate();">';
				html += '<div class="item-desc mt-10"><div class="title">' + leaveJson[i].date_from + ' To ' + leaveJson[i].date_to + '</div><div class="date"></div><div class="mt-10"><b>Status:</b> ' + leaveJson[i].status +'</div></div><div class="item-info2"></div></div>';
				}
			}
			
			if(leaveEntry > 10){
				html += '<select id="developer_page" onchange="leave_pageNavigation()">';
				var page = 1;
				for(var i = leaveEntry; i > 0; i -=10){
					if(page == leaveCurrentPage){
						html += '<option value="' + page + '" selected>' + 'Page ' + page + '</option>';
					}
					else html += '<option value="' + page + '">' + 'Page ' + page + '</option>';
					page++;
				}
				html += '</select>';
			}
		}
		else{
			html += '<div style="text-align: center"><b>No results found.</b></div>';
		}
		$('#leave .content').html(html);
		$('#leave .content').show();
		$('#leave').removeClass('loading');
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			popPage();
		}, 'No internet connection');
		$('#leave .content').show();
		$('#leave').removeClass('loading');
	});
}


function leave_pageNavigation()
{
	var pageSelected = document.getElementById("leave_page")
	leaveCurrentPage = pageSelected.options[pageSelected.options.selectedIndex].value;
	leave_load();
	$('#leave').addClass('loading');
	$('#leave .content').hide();
}


function leave_onDetails(index)
{
	var html = '<div class="item clearfix">';
		html += '<div class="title">' + leaveJson[index].date_from + ' To ' + leaveJson[index].date_to + '</div>';
		html += '<div class="mt-10"><b>Status:</b> ' + leaveJson[index].status + '</div>';
		
		html += '</div>';
		
	$('#leave-detail .content').html(html);
}
/* -------------------------------------------------- Leave Register -------------------------------------------------- */

function leave_register(){
	
	var fdate = document.getElementById("from_date").value;
	var tdate = document.getElementById("to_date").value;
	
	fdate = encodeURI(fdate);
	tdate = encodeURI(tdate);
	
	
	if(fdate == ""){
		navigator.notification.alert('Plesae select date from.', function()
		{
			$('#leave-request #from_date').focus();
		}, 'Missing info');
		return;
	}
	
	if(tdate == ""){
		navigator.notification.alert('Plesae select date to.', function()
		{
			$('#leave-request #to_date').focus();
		}, 'Missing info');
		return;
	}
	
	$('#leave-request').addClass('loading');
	$('#leave-request .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/leave_register.php',
		method: 'post',
		cache: false,
		data:
		{
			sales: uid,
			date_from: fdate,
			date_to: tdate
		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#leave-request').removeClass('loading');
			$('#leave-request .content').show();
			navigator.notification.alert('You have successfully registered.', null, 'Successful');
			
			$('#from_date').val(null);
			$('#to_date').val(null);
			popPage();
		}
		else
		{
			navigator.notification.alert('An error has occur, please try again later.', null, 'Error');
			$('#leave-request').removeClass('loading');
			$('#leave-request .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#leave-request').removeClass('loading');
		$('#leave-request .content').show();
	});
}


/* --------------------------------------------------Evaluation Register ----------------------- */
function evaluation_register(){

	var meeting_status = document.getElementById("eva_status").value;
	var meeting_pax = document.getElementById("eva_pax").value;
	var meeting_remark = document.getElementById("eva_remark").value;
	
	meeting_status = encodeURI(meeting_status);
	meeting_pax = encodeURI(meeting_pax);
	meeting_remark = encodeURI(meeting_remark);
	
	
	if(meeting_status == ""){
		navigator.notification.alert('Plesae select visit date.', function()
		{
			$('#meeting_evaluation #eva_status').focus();
		}, 'Missing info');
		return;
	}
	
	if(meeting_pax == ""){
		navigator.notification.alert('Plesae select estimated visit time.', function()
		{
			$('#meeting_evaluation #eva_pax').focus();
		}, 'Missing info');
		return;
	}
	
	if(meeting_remark == ""){
		navigator.notification.alert('Plesae enter visitor car plate number.', function()
		{
			$('#meeting_evaluation #eva_remark').focus();
		}, 'Missing info');
		return;
	}
	

	$('#meeting_evaluation').addClass('loading');
	$('#meeting_evaluation .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/evaluation_register.php',
		method: 'post',
		cache: false,
		data:
		{
		
			user: uid,
			evaluation_status: meeting_status,
			evaluation_pax: meeting_pax,
			evaluation_remark: meeting_remark
		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#meeting_evaluation').removeClass('loading');
			$('#meeting_evaluation .content').show();
			navigator.notification.alert('You have successfully registered your evaluation.', null, 'Successful');
			
			$('#eva_status').val(null);
			$('#eva_pax').val(null);
			$('#eva_remark').val(null);
		}
		else
		{
			navigator.notification.alert('An error has occur, please try again later.', null, 'Error');
			$('#meeting_evaluation').removeClass('loading');
			$('#meeting_evaluation .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Please check your internet connection and try again.', null, 'No internet connection');
		$('#meeting_evaluation').removeClass('loading');
		$('#meeting_evaluation .content').show();
	});

}

	
	

/* -------------------------------------------------- Date Pick -------------------------------------------------- */
function datePick(dateId){
	datePicker.show({
		date: new Date(),
		mode: 'date',
		allowFutureDates: false
	}, function(date){  
		var dateString = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
		$(dateId).val(dateString);
		if(dateId == '#mcl_fromDate') window.mcl_fromDate = dataString;
		if(dateId == '#mcl_toDate') window.mcl_toDate = dataString;
	});
}

function leave_book_datePick(dateId){
	var today = new Date();
	if($(dateId).val()==''){
		datePicker.show({
			date: new Date(),
			mode: 'date',
			minDate: today
		}, function(date){  
			var dateString = date.getFullYear() + "-" + facility_book_addZero(date.getMonth()+1) + "-" + facility_book_addZero(date.getDate());
			$(dateId).val(dateString);
		});
	}
	else{
		var chosenDate = $(dateId).val();
		datePicker.show({
			date: new Date('"' + chosenDate + '"'),
			mode: 'date',
			minDate: today
		}, function(date){  
			var dateString = date.getFullYear() + "-" + facility_book_addZero(date.getMonth()+1) + "-" + facility_book_addZero(date.getDate());
			$(dateId).val(dateString);
		});
	}
}
/* -------------------------------------------------- Check In Page Functions -------------------------------------------------- */
 
// Find user location at checkin page 
function ci_findLocation() {
	var output = document.getElementById("ci_locationStatus");
		output.innerHTML ="Searching for location...";	
	
	navigator.geolocation.getCurrentPosition(ci_onSuccess, ci_onFail, {maximumAge: 0, timeout: 5000, enableHighAccuracy:true});
			
	function ci_onSuccess(position) {
		
		var latitude  = position.coords.latitude;
		var longitude = position.coords.longitude;
		//var timestamp = new Date (position.timestamp);
		
		window.ci_latitude = latitude;
		window.ci_longitude = longitude;
		//window.ci_timestamp = timestamp;	
	
		$('#ci_latitude').val(position.coords.latitude);
		$('#ci_longitude').val(position.coords.longitude);
		//$('#ci_timestamp').val(new Date (position.timestamp));
				
		var img = document.getElementById('ci_mapImg');
		var imgWidth = screen.width - 60;
		var imgHeight = Math.round(screen.width * 0.6);
		img.src = "http://maps.google.com/maps/api/staticmap?" + latitude + "," + longitude + "&zoom=15&size=" + imgWidth + "x" + imgHeight + "&maptype=roadmap&markers=" + latitude + "," + longitude;
		img.style.display = 'block';
		
		output.innerHTML ="Location found";
	};
			
	function ci_onFail() {
		navigator.notification.alert('Unable to retrieve your location, please enable your GPS and try again.', null, 'GPS not found');
		var output = document.getElementById("ci_locationStatus");
		output.innerHTML ="Location not found";
	};			
}


// Clear content of checkin page 
function ci_clearContent(){
	window.ci_latitude = null;
	window.ci_longitude = null;
	//window.ci_timestamp = null;
	window.ci_company = null;
	window.ci_description = null;
	$('#ci_latitude').val(null);
	$('#ci_longitude').val(null);
	//$('#ci_timestamp').val(null);
	$('#ci_company').val(null);
	$('#ci_description').val(null);
	var output = document.getElementById("ci_locationStatus");
		output.innerHTML = null;
	var img = document.getElementById('ci_mapImg');
		img.style.display = 'none';
}


// Submit checkin function
function ci_submit(){
	
	if(!loggedIn){
		navigator.notification.alert('Please log in to your account first.', function()
				{
					pushPage('profile', LTR);
				}, 'User not found');
		return;
	}
	
	if(window.ci_latitude == null){
		navigator.notification.alert('Please check in your location first.', null, 'Location not found');
		return; 
	}
	
	window.ci_company = document.getElementById("ci_company").value;
	window.ci_description = document.getElementById("ci_description").value;
	
	if(window.ci_company == ""){
		navigator.notification.alert('Please key in visiting company name or place.', function()
		{
			$('#checkin #ci_company').focus();
		}, 'Missing info');
		return; 
	}
	
	if(window.ci_description == ""){
		navigator.notification.alert('Please enter description.', function()
		{
			$('#checkin #ci_description').focus();
		}, 'Missing info');
		return; 
	}
	
	$('#checkin').addClass('loading');
	$('#checkin .content').hide();
	
	$.ajax(
	{
		
		url: 'http://sunday-tech.com/almushin/api/attendance_log.php?lang='+ ci_latitude +'&long='+ ci_longitude +'&company='+ ci_company +'&user='+ userIndex + '&description='+ ci_description,
		data:
		{

		}
	})
	.done(function(data)
	{
		ci_clearContent();
		$('#notes').html('');
		if($.trim(data) == '0')
			navigator.notification.alert('You have successfully checked in.', null, 'Successful');
		else
			navigator.notification.alert('Check in was denied by server, please contact administrator about this issue.', null, 'Error');
	})
	.fail(function()
	{
		$('#notes').html('');
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
	});
	
	$('#checkin').removeClass('loading');
	$('#checkin .content').show();
}




////////////////////////////////////////////////////////////////////////////////////////
/* Sales Functions */
////////////////////////////////////////////////////////////////////////////////////////

var saleEntry;
var saleCurrentPage = 1;
var saleJson;

function sale_checkPage()
{

//alert(uid);
	$('#sale').addClass('loading');
	$('#sale .content').hide();
	if(isLogin());
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/sale_summarylist.php',
		method: 'post',
		cache: false,
		data:
		{
			sales: uid
		}
	})
	.done(function(response){
		var json = $.parseJSON(response);
		
		window.appoinment_id = json[1].index;

		var html = '';
		if(json.length > 1){
			for(var i = 1; i < json.length; i++){
				window.index = json[i].index;
				html += '<div class="item clearfix">';
				html += '<div class="item-desc" ><div class="title">' + json[i].name + '</div><div class="date"><i style="padding-right:5px" class="icon ion-calendar"></i> ' + json[i].date + '</div><br><div class="text"><b>Total:</b> ' + json[i].total + '<br><b>Delivered:</b> ' + json[i].delivered + '<br><b>Pending:</b> ' + json[i].pending + '<br><b>Cancel:</b> ' + json[i].cancel + '</div></div>';
				html += '<div><a class="button button_wrapper mb-18" onClick="pushPage(\'new_sale\', RTL);loadproduct(); loadcolor();assigncustomer(' + json[i].index + ');playMP3(); vibrate();"> Add New </a>';
				html += '<a class="button button_wrapper mb-18" onClick="sale_load(' + json[i].index + '); pushPage(' + "'customer_detail'" + '); vibrate(); playMP3();"> View</a>';
				html += '<a class="button button_wrapper mb-18" onClick="onPrint(' + json[i].index + '); vibrate(); playMP3(); "> Print </a>';
				html += '<a class="button button_wrapper mb-18" onClick="onDelete(confirm); vibrate(); playMP3();"> Delete</a></div></div>';
			}
		}
		else{
			html += '<div style="text-align: center"><b>No results found.</b></div>';
		}
		$('#sale .content').html(html);
		$('#sale .content').show();
		$('#sale').removeClass('loading');

		sale_load(); 
	})

	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			popPage();
		}, 'No internet connection');
		$('#sale').removeClass('loading');
		$('#sale .content').show();
	});
}
function assigncustomer(cid)
{	
window.appoinment_id = cid;
//alert(cid);
}
function sale_load(aid)
{	
	$('#customer_detail').addClass('loading');
	$('#customer_detail .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/sale_counter.php',
		method: 'post',
		cache: false,
		data:
		{
			page: saleCurrentPage,
			appoinment_id: aid

		}
	})
	.done(function(response)
	{
		saleJson = $.parseJSON(response);

				
		
		if(saleJson.length > 1){
			
			var html = '';
			
			for(var i = 1; i < saleJson.length; i++){
				html += '<div class="item clearfix">';
				html += '<div class="title">Customer Name:<br>' + saleJson[i].name + '</div>';
				html += '<div class="date"><i style="padding-right:5px" class="icon ion-calendar"></i> ' + saleJson[i].date + '</div>';
				html += '<div class="text mt-10"><b>Product Quality:</b>' + saleJson[i].product_quantity + '</div>';
				html += '<div class="text mt-10"><b>Total:</b>' + saleJson[i].total + '</div>';
				html += '<div><a class="button button_wrapper mb-18" onClick="sale_onDetails(' + i + '); pushPage(' + "'sale_detail'" + '); vibrate(); playMP3();">View</a>';
				html += '<a class="button button_wrapper mb-18" onClick="update_order('+i+'); vibrate(); playMP3();"> Update </a>';
				html += '<a class="button button_wrapper mb-18" onClick="customerDelete(); vibrate(); playMP3();"> Delete</a></div></div>';


				if(saleEntry > 10)
				{
					html += '<select id="sale_page" onchange="sale_pageNavigation()">';
					var page = 1;
					for(var i = saleEntry; i > 0; i -=10){
						if(page == saleCurrentPage){
							html += '<option value="' + page + '" selected>' + 'Page ' + page + '</option>';
						}
						else html += '<option value="' + page + '">' + 'Page ' + page + '</option>';
						page++;
					}
					html += '</select>';
				}

			}
		}
		else{
			html += '<div style="text-align: center"><b>No results found.</b></div>';
		}
		$('#customer_detail .content').html(html);
		$('#customer_detail .content').show();
		$('#customer_detail').removeClass('loading');
		
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			popPage();
		}, 'No internet connection');
		$('#customer_detail').removeClass('loading');
		$('#customer_detail .content').show();
	});
}



function sale_pageNavigation()
{
	var pageSelected = document.getElementById("sale_page")
	saleCurrentPage = pageSelected.options[pageSelected.options.selectedIndex].value;
	sale_load();
	$('#customer_detail').addClass('loading');
	$('#customer_detail .content').hide();
}




function onPrint(index)
{
	if (confirm('Do you want print this details!')) {
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/print_status.php',
		method: 'post',
		cache: false,
		data:
		{
			index:index
		}
	})
	.done(function(response){
		var json = $.parseJSON(response);
		
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			
		}, 'No internet connection');
		
	});

	}
	
}


function sale_onDetails(index)
{
		
		var html = '<div class="item clearfix">';
	
		html += '<div class="mt-10 first-desc"><b>Name:</b><br> ' + saleJson[index].name + '</div>';
		html += '<div class="mt-10 text"><b>Status:</b><br> ' + saleJson[index].status + '</div>';
		html += '<div class="mt-10 text"><b>Contact No:</b><br> ' + saleJson[index].contact + '</div><br>';
		
		html += '<div class="mt-10 first-desc"><b>Product:</b><br> ' + saleJson[index].product_quantity + '</div>';
		html += '<div class="mt-10 text"><b>Discount:</b><br>RM ' + saleJson[index].discount + '</div>';
		html += '<div class="mt-10 text"><b>Total:</b><br>RM ' + saleJson[index].total + '</div><br>';
		
		html += '<div class="mt-10 first-desc"><b>Payment Method:</b><br> ' + saleJson[index].payment_method + '</div>';
		html += '<div class="mt-10 text"><b>First Payment:</b><br>RM ' + saleJson[index].first_payment + '</div>';
		html += '<div class="mt-10 first-desc"><b>Pending Payment:</b><br>RM ' + saleJson[index].pending_payment + '</div>';
		
		html += '<div class="mt-10 text"><b>Warranty:</b><br> ' + saleJson[index].warranty + '</div>';
		
		html += '<br><div class="mt-10 text"><b>Remarks:</b><br> ' + saleJson[index].remarks + '</div></div>';

		
		html += '<a href="tel:' + saleJson[index].contact + '" onClick="playMP3();" class="button fit mb-18"><i style="float:left;" class="icon ion-android-call"></i>  Call</a>';
		
		
		
	$('#sale_detail .content').html(html);
}

function onDelete()
{
	if (confirm('Are you sure you want to delete this record?')) {
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/delete_sale_list.php',
		method: 'post',
		cache: false,
		data:
		{
			appoinment_id: window.appoinment_id,
		}
	})
	.done(function(response){

	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			
		}, 'No internet connection');
		
	});

	}
	
}



/* Update Customer Details */
function update_order(index){
	pushPage('edit_customer');
	window.index = saleJson[index].index;
	window.order_name = saleJson[index].name;
	window.order_status = saleJson[index].status;
	window.order_contact = saleJson[index].contact;
	window.order_product = saleJson[index].product_quantity;
	window.order_discount = saleJson[index].discount;
	window.order_total = saleJson[index].total;
	window.order_payment_method = saleJson[index].payment_method;
	window.order_first_payment = saleJson[index].first_payment;
	window.order_pending_payment = saleJson[index].pending_payment;
	window.order_warranty = saleJson[index].warranty;
	window.order_remarks= saleJson[index].remarks;

	$('#edit_customer #name').val(window.order_name);
	$('#edit_customer #status').val(window.order_status);
	$('#edit_customer #contact').val(window.order_contact);
	$('#edit_customer #product').val(window.order_product);
	$('#edit_customer #discount').val(window.order_discount);
	$('#edit_customer #total').val(window.order_total);
	$('#edit_customer #payment').val(window.order_payment_method);
	$('#edit_customer #first').val(window.order_first_payment);
	$('#edit_customer #pending').val(window.order_pending_payment);
	$('#edit_customer #warranty').val(window.order_warranty);
	$('#edit_customer #remarks').val(window.order_remarks);
}


function edit_customer_details(){

	var c_name = $.trim($('#edit_customer #name').val());
	var c_status = $.trim($('#edit_customer #status').val());
	var c_contact = $.trim($('#edit_customer #contact').val());
	var c_product = $.trim($('#edit_customer #product').val());
	var c_discount = $.trim($('#edit_customer #discount').val());
	var c_total = $.trim($('#edit_customer #total').val());
	var c_payment = $.trim($('#edit_customer #payment').val());
	var c_first = $.trim($('#edit_customer #first').val());
	var c_pending = $.trim($('#edit_customer #pending').val());
	var c_warranty = $.trim($('#edit_customer #warranty').val());
	var c_remarks = $.trim($('#edit_customer #remarks').val());

	

	if(c_name == ""){
		navigator.notification.alert('Please enter customer name.', function()
		{
			$('#edit_customer #name').focus();
		}, 'Missing info');
		return;
	}
	if(c_status == ""){
		navigator.notification.alert('Please enter customer status.', function()
		{
			$('#edit_customer #status').focus();
		}, 'Missing info');
		return;
	}
	if(c_contact == ""){
		navigator.notification.alert('Please enter customer contact.', function()
		{
			$('#edit_customer #contact').focus();
		}, 'Missing info');
		return;
	}
	if(c_product == ""){
		navigator.notification.alert('Please enter selected product.', function()
		{
			$('#edit_customer #product').focus();
		}, 'Missing info');
		return;
	}
	if(c_discount == ""){
		navigator.notification.alert('Please enter discount.', function()
		{
			$('#edit_customer #discount').focus();
		}, 'Missing info');
		return;
	}
	if(c_total == ""){
		navigator.notification.alert('Please enter total.', function()
		{
			$('#edit_customer total').focus();
		}, 'Missing info');
		return;
	}
	if(c_first == ""){
		navigator.notification.alert('Please enter customer first payment.', function()
		{
			$('#edit_customer #first').focus();
		}, 'Missing info');
		return;
	}
	if(c_pending == ""){
		navigator.notification.alert('Please enter customer pending payment.', function()
		{
			$('#edit_customer #pending').focus();
		}, 'Missing info');
		return;
	}

	if(c_warranty == ""){
		navigator.notification.alert('Please enter customer given warranty.', function()
		{
			$('#edit_customer #warranty').focus();
		}, 'Missing info');
		return;
	}
	if(c_remarks == ""){
		navigator.notification.alert('Please enter customer remarks.', function()
		{
			$('#edit_customer #remarks').focus();
		}, 'Missing info');
		return;
	}
	
	$('#edit_customer').addClass('loading');
	$('#edit_customer .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/customer_profile_update.php',
		method: 'post',
		cache: false,
		data:
		{
			index: window.index,
			name:c_name,
			status:c_status,
			contact:c_contact,
			product:c_product,
			discount:c_discount,
			total:c_total,
			payment:c_payment,
			first:c_first,
			pending:c_pending,
			warranty:c_warranty,
			remarks:c_remarks	
		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#edit_customer').removeClass('loading');
			$('#edit_customer .content').show();
			navigator.notification.alert('You have successfully updated your customer details.', null, 'Successful');
			
			popPage();
		}
		else
		{
			navigator.notification.alert('An error has occur, please try again later.', null, 'Error');
			$('#edit_customer').removeClass('loading');
			$('#edit_customer .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#edit_customer').removeClass('loading');
		$('#edit_customer .content').show();
	});
	
} 



function customerDelete()
{
	if (confirm('Are you sure you want to delete this record?')) {
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/customer_delete.php',
		method: 'post',
		cache: false,
		data:
		{
			index: window.index
		}
	})
	.done(function(response){

	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			
		}, 'No internet connection');
		
	});

	}
}





function sale_register(){
	//alert("d");
	var c_name = document.getElementById("customer_name").value;
	
	var products_1 = document.getElementById("products_1").value;
	var qty_1 = document.getElementById("qty_1").value;
	var color_1 = document.getElementById("color_1").value;
	
	var products_2 = document.getElementById("products_2").value;
	var qty_2 = document.getElementById("qty_2").value;
	var color_2 = document.getElementById("color_2").value;
	
	var products_3 = document.getElementById("products_3").value;
	var qty_3 = document.getElementById("qty_3").value;
	var color_3 = document.getElementById("color_3").value;
	
	var products_4 = document.getElementById("products_4").value;
	var qty_4 = document.getElementById("qty_4").value;
	var color_4 = document.getElementById("color_4").value;
	
	var products_5 = document.getElementById("products_5").value;
	var qty_5 = document.getElementById("qty_5").value;
	var color_5 = document.getElementById("color_5").value;
	
	
	
	
	var contact = document.getElementById("contact").value;
	var discount = document.getElementById("discount").value;
	var total = document.getElementById("total").value;
	var remark = document.getElementById("remark").value;
	var delevery_add = document.getElementById("getAddress").value;
	var delevery_date = document.getElementById("datePicker").value;
	
	c_name = encodeURI(c_name);
	product = encodeURI(product);
	quanity = encodeURI(quanity);
	contact = encodeURI(contact);
	discount = encodeURI(discount);
	total = encodeURI(total);
	remark = encodeURI(remark);
	delevery_add = encodeURI(delevery_add);
	delevery_date = encodeURI(delevery_date);
	
	
	
	
	$('#new_sale').addClass('loading');
	$('#new_sale .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/sale_register.php',
		method: 'post',
		cache: false,
		data:
		{
			sales: uid,
			appoinment_id: window.appoinment_id,
			name: c_name,
			contact: contact,
		    product_quantity: product + "," + quanity,
			discount: discount,
			total: total,
			remarks: remark,
			deliv_date: delevery_date,
			adres:delevery_add
		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#new_sale').removeClass('loading');
			$('#new_sale .content').show();
			navigator.notification.alert('You have successfully registered your customer.', null, 'Successful');
			
			$('#customer_name').val(null);
			$('#products_1').val(null);
			$('#qty_1').val(null);
			$('#contact').val(null);
			$('#discount').val(null);
			$('#total').val(null);
			$('#remark').val(null);
			$('#getAddress').val(null);
			$('#datePicker').val(null);
			
			popPage();
		}
		else
		{
			navigator.notification.alert('An error has occur, please try again later.', null, 'Error');
			$('#new_sale').removeClass('loading');
			$('#new_sale .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#new_sale').removeClass('loading');
		$('#new_sale .content').show();
	});
}

function clearSalesInfo(){

	var c_name = document.getElementById("customer_name").value;
	var product = document.getElementById("searchproduct").value;
	var quanity = document.getElementById("number0").value;
	var contact = document.getElementById("contact").value;
	var discount = document.getElementById("discount").value;
	var total = document.getElementById("total").value;
	var remark = document.getElementById("remark").value;
	var delevery_add = document.getElementById("getAddress").value;
	var delevery_date = document.getElementById("datePicker").value;
	
	c_name = encodeURI(c_name);
	product = encodeURI(product);
	quanity = encodeURI(quanity);
	contact = encodeURI(contact);
	discount = encodeURI(discount);
	total = encodeURI(total);
	remark = encodeURI(remark);
	delevery_add = encodeURI(delevery_add);
	delevery_date = encodeURI(delevery_date);
	
	if(delevery_add == ""){
		navigator.notification.alert('Please enter delevery address.', function()
		{
			$('#new_sale #delevery_add').focus();
		}, 'Missing info');
		return;
	}

	if(delevery_date == ""){
		navigator.notification.alert('Please enter delevery date.', function()
		{
			$('#new_sale #delevery_date').focus();
		}, 'Missing info');
		return;
	}

	if(c_name == ""){
		navigator.notification.alert('Please enter customer name.', function()
		{
			$('#new_sale #customer_name').focus();
		}, 'Missing info');
		return;
	}
	
	if(product == ""){
		navigator.notification.alert('Please select product.', function()
		{
			$('#new_sale #searchproduct').focus();
		}, 'Missing info');
		return;
	}
	
	if(quanity == ""){
		navigator.notification.alert('Please enter quantity.', function()
		{
			$('#new_sale #number0').focus();
		}, 'Missing info');
		return;
	}
	
	if(contact == ""){
		navigator.notification.alert('Please enter contact number.', function()
		{
			$('#new_sale #contact').focus();
		}, 'Missing info');
		return;
	}
	
	if(remark == ""){
		navigator.notification.alert('Please enter your remarks.', function()
		{
			$('#new_sale #remark').focus();
		}, 'Missing info');
		return;
	}
	
	$('#new_sale').addClass('loading');
	$('#new_sale .content').hide();
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/sale_register1.php',
		method: 'post',
		cache: false,
		data:
		{
			sales: uid,
			appoinment_id: window.appoinment_id,
			name: c_name,
			contact: contact,
			product_quantity: product + "," + quanity,
			discount: discount,
			total: total,
			remarks: remark,
			deliv_date: delevery_date,
			adres:delevery_add

		}
	})
	.done(function(response)
	{
		if($.trim(response) == '0')
		{
			$('#new_sale').removeClass('loading');
			$('#new_sale .content').show();
			navigator.notification.alert('You have successfully registered your customer.', null, 'Successful');
			
			$('#customer_name').val(null);
			$('#searchproduct').val(null);
			$('#number0').val(null);
			$('#contact').val(null);
			$('#discount').val(null);
			$('#total').val(null);
			$('#remark').val(null);
			$('#getAddress').val(null);
			$('#datePicker').val(null);
			
			popPage();
		}
		else
		{
			navigator.notification.alert('An error has occur, please try again later.', null, 'Error');
			$('#new_sale').removeClass('loading');
			$('#new_sale .content').show();
		}
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', null, 'No internet connection');
		$('#new_sale').removeClass('loading');
		$('#new_sale .content').show();
	});
	

	$('#customer_name').val(null);
	$('#searchproduct').val('Select Product');
	$('#number0').val('0');
	$('#contact').val(null);
	$('#discount').val(null);
	$('#total').val(null);
	$('#remark').val(null);
	$('#getAddress').val(null);
	$('#datePicker').val(null);
	$('#new_sale .content').html(html); 

}

/* -------------------------------------------------- Product List -------------------------------------------------- */

function loadproduct()
{
	$.ajax(
	{
	    url: 'http://sunday-tech.com/osafe/api/product_category.php',
		method: 'get',
		cache: false,
	})
	.done(function(response)
	{
			var json = $.parseJSON(response);
				
		    window.search_product= json[0].name;
			window.search_unit_price= json[0].unit_price;
			var object_p1 = document.getElementById('products_1');
    		object_p1.value=window.search_product;
			object_p1.value=window.search_unit_price;
			var html = '';
			html += '<option value ="0">Select Product</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].unit_price + '">' + json[i].name + '</option>';
			} 
			
			$('#products_1').html(html);
			
			
			
			window.search_product= json[1].name;
			window.search_unit_price= json[1].unit_price;
			var object_p2 = document.getElementById('products_2');
    		object_p2.value=window.search_product;
			object_p2.value=window.search_unit_price;
			var html = '';
			html += '<option value ="0">Select Product</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].unit_price + '">' + json[i].name + '</option>';
			} 
			
			$('#products_2').html(html);
			
			
			
			
			window.search_product= json[2].name;
			window.search_unit_price= json[2].unit_price;
			var object_p3 = document.getElementById('products_3');
    		object_p3.value=window.search_product;
			object_p3.value=window.search_unit_price;
			var html = '';
			html += '<option value ="0">Select Product</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].unit_price + '">' + json[i].name + '</option>';
			} 
			
			$('#products_3').html(html);
			
			
			
			
			window.search_product= json[3].name;
			window.search_unit_price= json[3].unit_price;
			var object_p4 = document.getElementById('products_4');
    		object_p4.value=window.search_product;
			object_p4.value=window.search_unit_price;
			var html = '';
			html += '<option value ="0">Select Product</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].unit_price + '">' + json[i].name + '</option>';
			} 
			
			$('#products_4').html(html);
			
			
			
			window.search_product= json[4].name;
			window.search_unit_price= json[4].unit_price;
			var object_p5 = document.getElementById('products_5');
    		object_p5.value=window.search_product;
			object_p5.value=window.search_unit_price;
			var html = '';
			html += '<option value ="0">Select Product</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].unit_price + '">' + json[i].name + '</option>';
			} 
			
			$('#products_5').html(html);
			
			
			
			
			
	})
	.fail(function()
	{
		 
	});
	
	//alert("s");
	$.ajax(
	{
	    url: 'http://sunday-tech.com/osafe/api/color.php',
		method: 'get',
		cache: false,
	})
	.done(function(response)
	{
			var json = $.parseJSON(response);
				
		    window.search_product_color= json[0].color;
			var color_1 = document.getElementById('color_1');
    		color_1.value=window.search_product;
			color_1.value=window.search_product_color;
			var html = '';
			html += '<option value ="0">Select Color</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].color + '">' + json[i].color + '</option>';
			} 
			$('#color_1').html(html);
			
			window.search_product_color= json[0].color;
			var color_2 = document.getElementById('color_2');
    		color_2.value=window.search_product;
			color_2.value=window.search_product_color;
			var html = '';
			html += '<option value ="0">Select Color</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].color + '">' + json[i].color + '</option>';
			} 
			$('#color_2').html(html);
			
			window.search_product_color= json[0].color;
			var color_3 = document.getElementById('color_3');
    		color_3.value=window.search_product;
			color_3.value=window.search_product_color;
			var html = '';
			html += '<option value ="0">Select Color</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].color + '">' + json[i].color + '</option>';
			} 
			$('#color_3').html(html);
			
			window.search_product_color= json[0].color;
			var color_4 = document.getElementById('color_4');
    		color_4.value=window.search_product;
			color_4.value=window.search_product_color;
			var html = '';
			html += '<option value ="0">Select Color</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].color + '">' + json[i].color + '</option>';
			} 
			$('#color_4').html(html);
			
			window.search_product_color= json[0].color;
			var color_5 = document.getElementById('color_5');
    		color_5.value=window.search_product;
			color_5.value=window.search_product_color;
			var html = '';
			html += '<option value ="0">Select Color</option>';
			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].color + '">' + json[i].color + '</option>';
			} 
			$('#color_5').html(html);
			
			
	})
	.fail(function()
	{
		 
	});
}

function loadcolor()
{

}

/* -------------------------------------------------- Evaluation List -------------------------------------------------- */

function loadevaluation()
{
	$.ajax(
	{
	//	url: 'http://localhost/kelakar/api/video_details.php?id=1',
	    url: 'http://sunday-tech.com/osafe/api/status_category.php',
		method: 'get',
		cache: false,
	})
	.done(function(response)
	{
			var json = $.parseJSON(response);
				
		    window.search_category= json[1].status;
			var object = document.getElementById('eva_status');
    		object.value=window.search_category;
			var html = '';
			html += '<option>Select Meeting Evaluation</option>';

			for(var i = 1; i < json.length; i++){
				html += '<option value="' + json[i].status + '">' + json[i].status + '</option>';
			}
		 	
			$('#eva_status').html(html);
	})
	.fail(function()
	{
		
	});
}







