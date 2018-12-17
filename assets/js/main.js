var TMAX = 3;
var TMX = new Array(TMAX);
var NON = 0;
var RTL = 1;
var LTR = TMAX - RTL;
var transition = NON;


/* Document ready handler */

function onReady()
{
	var width = $(document).width();
	TMX[RTL] = [width, 0];
	TMX[LTR] = [-width, 0];
	
	$('.tab-button').click(function()
	{
		$('.tab-button.active').removeClass('active');
		$(this).addClass('active');
	});
	
	$('.tab-button1').click(function()
	{
		$('.tab-button1.active').removeClass('active');
		$(this).addClass('active');
	});
	
	if(window.location.hash != '') window.location.hash = '';
	setTimeout(function()
	{ 
		$('#home').addClass('active').show(0);
		$('#splash').fadeOut(500, function() { $(this).remove(); });
		appm_calendarGenerate();
	}, 2000);

	//var fbid = window.localStorage.getItem("key3");
	var username = window.localStorage.getItem("key");
	var password = window.localStorage.getItem("key2");
	if(username){    //alert(1)
		autoLogin(username, password);
	}
}

function onMonthChange()
{
	$('.day[data-time="1437314400000"]').addClass('event');
	$('.day[data-time="1438264800000"]').addClass('event');
	$('.day').click(function()
	{
		$('.events').stop().hide(0);
		if($(this).attr('data-time') == '1437314400000')
		{
			$('.events .event-item').remove();
			$('.events').append('<div class="event-item">8:00am - School Opening</div>').fadeIn(300);
		}
		else if($(this).attr('data-time') == '1438264800000')
		{
			$('.events .event-item').remove();
			$('.events').append('<div class="event-item">9:00am - Team Building</div>').fadeIn(300);
		}
	});
	
	$('.next, .prev').click(function()
	{
		$('.events').stop().hide(0);
		setTimeout(onMonthChange, 100);
	});
}


/* Window load handler */

function onLoad()
{
	$(window).bind('hashchange', hashChangeHandler);
	document.addEventListener('backbutton', backButtonHandler, false);
}


/* Hash change handler */

function hashChangeHandler()
{
	eval($('.page.active').attr('onClose'));
	if(transition)
		$('.page.active').stop().removeClass('active').animate({ left:TMX[TMAX-transition][0], top:TMX[TMAX-transition][1] }, { easing:'easeOutQuint', duration:300, complete:function() { $(this).hide(0); }});
	else
		$('.page.active').stop().removeClass('active').hide(0);
	
	var hash = window.location.hash;
	if(hash == '' || hash == '#') hash = '#home';
	if(transition)
		$(hash).stop().addClass('active').css({ left:TMX[transition][0], top:TMX[transition][1], display:'block' }).animate({ left:0, top:0 }, { easing:'easeOutQuint', duration:300 });
	else
		$(hash).stop().addClass('active').css({ left:0, top:0, display:'block' });
	eval($(hash).attr('onOpen'));
}


/* Back button handler */
var backCount = 0;
var hasPushPage = false;
function backButtonHandler(evt)
{
	evt.preventDefault();
	evt.stopPropagation();
	if(window.location.hash == '#home' || window.location.hash =='#profile' || !hasPushPage){
		backCount++;
		if(backCount >= 2) navigator.notification.confirm("Are you sure want to exit?", onConfirmExit, "Confirmation", "Yes,No"); 
		return;
	} else {
		popPage();
	}
}

function onConfirmExit(button) {
    if(button==2){ //If User select a No, then return back;
        return;
    }else{
        navigator.app.exitApp(); // If user select a Yes, quit from the app.
    }
}

/* Push page */
function pushPage(id, animation)
{
	backCount = 0;
	transition = (animation ? animation : NON);
	window.location.hash = id;
}

/* Pop page */
function popPage()
{
	backCount = 0;
	transition = (transition ? TMAX - transition : NON);
	window.history.back();
}

/* Login button clicked */

function onLogin()
{
	var username = $.trim($('#login #username').val());
	var password = $('#login #password').val();
	
	
	
	//loginCallback = function()
	//{
	//	pushPage('profile', LTR);
	//};
	
	
	$('#login').addClass('loading');
	$('#login .content').hide();
	
	$.ajax(
	{
		url: 'http://apemalaysia.net/osafe/api/login.php',
		
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
				
				//navigator.notification.alert('Login', null, 'Successful');
				window.localStorage.setItem("key", username);
				window.localStorage.setItem("key2", password);
				
				//deviceID = json[1].deviceid;
				window.uid= json[1].index;
				window.myphone = json[1].mobile;
				window.myemail = json[1].email;
				window.myage = json[1].age;
				

				//deviceID = RegID;
				//updateDeviceId();
				
				var html = '<div class="item clearfix"><div style="border-bottom: 2px solid #eee; padding-bottom: 20px;"><img class="avatar" id="profile_pic" onClick="profile_picChange();playMP3(); vibrate();" src="http://apemalaysia.net/osafe/upload/' + json[1].photo_url + '"></div>';
				
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
				$('#profile .content').show();
				pushPage('profile');
				
				
				
				alert(loggedIn);
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


/* Logout button clicked */

function onLogout()
{
	logout();
	/*navigator.notification.confirm
	(
		'Do you want to logout?',
		function(buttonIndex)
		{
			if(buttonIndex == 1) logout();
		},
		'Confirm logout',
		['Logout', 'Cancel']
	);*/
}

function onfbLogout()
{
	navigator.notification.confirm
	(
		'Do you want to logout?',
		function(buttonIndex)
		{
			if(buttonIndex == 1) fblogout();logout();
		},
		'Confirm logout',
		['Logout', 'Cancel']
	);
}
/* Register button clicked */

function onRegister()
{
	var fullName	= $.trim($('#register #full-name').val());
	var gender		= $('#register #gender').val();
	var dob			= $('#register #dob').val();
	var mobile		= $.trim($('#register #mobile').val());
	var email		= $.trim($('#register #email').val());
	var username	= $.trim($('#register #user-name').val());
	var password	= $.trim($('#register #password').val());
	var password2	= $.trim($('#register #password2').val());

	if(fullName == '')
	{
		navigator.notification.alert('Please enter your full name', function()
		{
			$('#register #full-name').focus();
		}, 'Missing info');
		return;
	}
	if(username == '')
	{
		navigator.notification.alert('Please enter your user name', function()
		{
			$('#register #user-name').focus();
		}, 'Missing info');
		return;
	}
	if(!isPhoneNumber(mobile))
	{
		navigator.notification.alert('Please enter your valid mobile number', function()
		{
			$('#register #mobile').focus();
		}, 'Missing info');
		return;
	}
	if(!isEmailAddress(email))
	{
		navigator.notification.alert('Please enter your valid email address', function()
		{
			$('#register #email').focus();
		}, 'Missing info');
		return;
	}
	if(password == '' || password.length < 6)
	{
		navigator.notification.alert('Please enter your password with at least 6 characters', function()
		{
			$('#register #password').focus();
		}, 'Error');
		return;
	}
	if(password2 != password)
	{
		navigator.notification.alert('Password does not match', function()
		{
			$('#register #password2').focus();
		}, 'Error');
		return;
	}
	
	register(fullName, username, password, mobile, email, dob, gender);
}



/* Validate email address */

function isEmailAddress(email)
{
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}


/* Validate phone number */

function isPhoneNumber(num)
{
	var intRegex = /[0-9 -()+]+$/;
	return (num.length >= 9 && intRegex.test(num));
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
/* Execution */

$(onReady);
$(window).load(onLoad);