
// these are labels for the days of the week
var cal_days_labels = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

// these are human-readable month name labels, in order
var cal_months_labels = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL',
                     'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER',
                     'OCTOBER', 'NOVEMBER', 'DECEMBER'];

// these are the days of the week for each month, in order
var cal_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

function call(phoneNo){
	window.open('tel:' + phoneNo, '_system');
}

var m_searchMonth;
var m_searchYear;
var searchDate;
var searchMonth;
var searchYear;
var appointmentJson;


function loadcupidapoint(date, month, year)
{ 
	$('#myapoint').addClass('loading');
	$('#myapoint .content').hide();

			var today_date = new Date();
	
			searchDate = (isNaN(date) || date == null) ? today_date.getDate() : date;
			searchMonth = (isNaN(month) || month == null) ? today_date.getMonth() : month;
			searchYear  = (isNaN(year) || year == null) ? today_date.getFullYear() : year;
			var monthName = cal_months_labels[searchMonth];

				
			var html = '<div class="segment-button">';
			
			var json = appointmentJson;
						
			for(var i = 1; i < json.length; i++)
			{
				var temp = new Date(json[i].date);
				if(temp.getDate() == searchDate && temp.getMonth() == searchMonth && temp.getFullYear() == searchYear) 
				{
						
					html += '<div class="item clearfix", onClick="onCupidapoint1(' + json[i].index + '); pushPage(' + "'myapointdetail'" + '); vibrate(); playMP3();">';
					html += '<div class="item-desc" ><div class="title">' + json[i].name + '</div><div class="date"></div><br><div class="text"><i style="padding-right:9px; padding-left:1px" class="icon ion-android-calendar"></i> ' + json[i].date + '<br><i style="padding-right:7px"  class="icon ion-clock"></i> ' + json[i].time_start + ' - ' + json[i].time_end + '<br><i style="padding-right:10px; padding-left:2px"  class="icon ion-ios-location"></i> ' + json[i].address_jalan + ','+json[i].address_taman+','+json[i].address_state+'</div></div><div class="item-info"></div></div>';
					
				}
				
			}
			$('#myapoint .content').html(html);
			$('#myapoint').removeClass('loading');
			$('#myapoint .content').show();
			
}
function onCupidapoint1(index)
{
	//alert(index);
	$('#myapointdetail').addClass('loading');
	$('#myapointdetail .content').hide();

	$.ajax(
	{
		
		url: 'http://sunday-tech.com/osafe/api/appointment_details.php?pid='+index,
		method: 'get',
		cache: false,
		data:
		{
			
		}
	})
	.done(function(response)
	{
	
				var json = $.parseJSON(response);
				window.appoinment_id = json[1].index;

			    //window.index1= json[1].index;
				$('#myapointdetail').removeClass('loading');
				
				var html = '<div class="item clearfix">';
				html += '<div class="item-desc mt-10"><div class="title">' + json[1].name + '</div>';
				
				html += '<div class="mt-10 first-desc"><b>Date:</b><br> ' + json[1].date + '</div>';
				
				html += '<div class="text mt-10"><b>Start Time:</b><br> ' + json[1].time_start + '</div>';
				
				html += '<div class="text mt-10"><b>End Time:</b><br> ' + json[1].time_end + '</div>';
				
				html += '<div class="text mt-10"><b>Location/Address:</br> ' + json[1].address_jalan + '  '+json[1].address_taman+'  '+json[1].address_state+', '+json[1].address_country+'</div><hr>';
				

				html += '<div class="title"><b>Contact Person One:</b></div>';
				
				if(json[1].pic1) html += '<div class="text mt-10"><b>Name:</b> ' + json[1].pic1 + '</div>';
				
				if(json[1].pic1_mobile) html += '<div class="text mt-10"><b>Mobile No:</b> ' + json[1].pic1_mobile + '</div>';
				
				if(json[1].pic1_tel) html += '<div class="text mt-10"><b>Telephone No:</b> ' + json[1].pic1_tel + '</div>';

				html +='<hr>';
				
				html += '<div class="title"><b>Contact Person Two:</b></div>';
				
				if(json[1].pic2) html += '<div class="text mt-10"><b>Name:</b> ' + json[1].pic2 + '</div>';
				
				if(json[1].pic2_mobile) html += '<div class="mt-10 text"><b>Mobile No:</b> ' + json[1].pic2_mobile + '</div>';
				
				if(json[1].pic2_tel) html += '<div class="mt-10 text"><b>Telephone No:</b> ' + json[1].pic2_tel + '</div>';

				html +='<hr>';

				if(json[1].options) html += '<div class="mt-10 text"><b>Option:</b> ' + json[1].options + '</div>';

				if(json[1].pax) html += '<div class="mt-10 text"><b>No. of Pax:</b> ' + json[1].pax + '</div><br>';

				if(json[1].remarks) html += '<div class="text"><b>Remarks:</b> ' + json[1].remarks + '</div></div>';

				html += '<a href="tel:' + json[1].pic1_mobile + '" class="button button_wrapper mb-18" onClick=" "><i style="float:left" class="icon ion-android-call"></i> '+json[1].pic1_mobile+'</a>';
				
				html += '<a href="http://maps.google.com/?q='+ json[1].address_lot +' '+ json[1].address_jalan +' '+ json[1].address_taman +'" class="button button_wrapper mb-18" onClick=" "><i style="float:left" class="icon ion-ios-location"></i> GPS</a>';

				html += '<a class="button button_wrapper mb-18" onClick=" "><i style="float:left" class="icon ion-compose"></i> File </a>';

				
				html += '</div><button class="fit mb-10" onClick="pushPage(\'checkin\', RTL); playMP3()"><i style="float:left;" class="icon ion-ios-location"></i> Check In</button>';
				html += '<a class="button fit mb-18" style="background:#333232;" onClick="pushPage(\'new_sale\', RTL);loadproduct();playMP3(); vibrate();"><i style="float:left" class="icon ion-compose"></i> New Sale</a>';
				html += '<a class="button fit mb-18" style="background:#333232;" onClick=" "><i style="float:left" class="icon ion-compose"></i> No Sales</a>';
				html += '<a class="button fit mb-18" style="background:#333232;" onClick="pushPage(\'meeting_evaluation\', RTL);loadevaluation();playMP3(); vibrate();"><i style="float:left" class="icon ion-compose"></i> Meeting Evaluation</a>';
				
			
				$('#myapointdetail .content').html(html);
				$('#myapointdetail').removeClass('loading');
				$('#myapointdetail .content').show();
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
			pushPage('home');
		}, 'No internet connection');
		$('#myapointdetail').removeClass('loading');
		$('#myapointdetail .homecontent').show();
	});		
}


function appm_calendarGenerate(month, year) 
{	
	$('#home').addClass('loading');
	
	var cal_current_date = new Date();
	m_searchMonth = (isNaN(month) || month == null) ? cal_current_date.getMonth() : month;
	m_searchYear  = (isNaN(year) || year == null) ? cal_current_date.getFullYear() : year;
	
	
	var firstDay = new Date(m_searchYear, m_searchMonth, 1);
	var startingDay = firstDay.getDay();
	var monthLength = cal_days_in_month[m_searchMonth];
	
	if (m_searchMonth == 1) { // February only
	  if ((m_searchYear % 4 == 0 && m_searchYear % 100 != 0) || m_searchYear % 400 == 0){
		monthLength = 29;
	  }
	}
	var monthName = cal_months_labels[m_searchMonth];
	var searchMonthReal = m_searchMonth + 1;
	if(searchMonthReal < 10) searchMonthReal = '0' + searchMonthReal;
	
	var month_search = m_searchYear + '-' + searchMonthReal;
	
	var html = '';	
		
		html += '<div class="segment-button">';
	
		html += '<div class="calRow mb-10 clearfix"><div class="calFirstRowLeft" onClick="appm_prevMonth(' + m_searchMonth + ',' + m_searchYear + '); vibrate(); playMP3();"><i class="icon ion-chevron-left"></i></div>';
		html += '<div class="calFirstRowCenter">' + monthName + ' ' + m_searchYear + '</div><div class="calFirstRowRight" onClick="appm_nxtMonth(' + m_searchMonth + ',' + m_searchYear + '); vibrate(); playMP3();"><i class="icon ion-chevron-right"></i></div></div>';
		
		html += '<div class="calRow">';
		for(var i = 0; i <= 6; i++){
			html += '<div class="calMidCol1">';
			html += cal_days_labels[i];
			html += '</div>';
		}
		html += '</div>';
		
		var day = 1;

	$('#home').removeClass('loading');
	$('#home .content').html(html);
	$('#home').removeClass('loading');
	if(isLogin());
	$.ajax(
	{
		url: 'http://sunday-tech.com/osafe/api/appointment_list.php',
		method: 'POST',
		cache: false,
		data:
		{
			user: uid
		}
	})
	.done(function(response)
	{	
		var json = $.parseJSON(response);
		appointmentJson = json;
		html = '';
		
		html += '<div class="segment-button">';
	
		html += '<div class="calRow mb-10 clearfix"><div class="calFirstRowLeft" onClick="appm_prevMonth(' + m_searchMonth + ',' + m_searchYear + '); vibrate(); playMP3();"><i class="icon ion-chevron-left"></i></div>';
		html += '<div class="calFirstRowCenter">' + monthName + ' ' + m_searchYear + '</div><div class="calFirstRowRight" onClick="appm_nxtMonth(' + m_searchMonth + ',' + m_searchYear + '); vibrate(); playMP3();"><i class="icon ion-chevron-right"></i></div></div>';
	
		html += '<div class="calRow">';
		for(var i = 0; i <= 6; i++){
			html += '<div class="calMidCol1">';
			html += cal_days_labels[i];
			html += '</div>';
		}
		html += '</div>';
		
		var day = 1;
		
		for(var i = 0; i < 7; i++){
			html += '<div class="calRow">';
			for(var j = 0; j <= 6; j++){
				
				if (day <= monthLength && (i > 0 || j >= startingDay)) {
					var appNumber = 0;
					for(var k = 1; k < json.length; k++){
						var temp = new Date(json[k].date);
						if(temp.getDate() == day && temp.getMonth() == m_searchMonth && temp.getFullYear() == m_searchYear) appNumber++;
					}
					if(appNumber > 0){
						html += '<div class="calMidColApp" onClick="pushPage(' + "'myapoint'" + '); loadcupidapoint(' + day + ',' + m_searchMonth + ',' + m_searchYear + '); vibrate(); playMP3();">';
						html += day;
						html += ' (' + appNumber + ')';
						day++;
					}
					else {
						html += '<div class="calMidCol">';
						html += day;
						day++;
					}
				}
				else {
					html += '<div class="calMidCol">';
					html += "&nbsp;";
				}
				html += '</div>';
			}
			html += '</div>';
			if (day > monthLength) break;
		}
		
		$('#home .content').html(html);
		$('#home').removeClass('loading');
		$('#home .content').show();
		
	})
	.fail(function(){
		navigator.notification.alert('Wifi connection required.\nConnect to Wi-Fi network and try again.', function()
		{
		}, 'No internet connection');
		$('#home').removeClass('loading');
		$('#home .content').show();
	});	
}


function appm_prevMonth(month, year){
	if(month != 0) appm_calendarGenerate(month - 1, year);
	else appm_calendarGenerate(11, year - 1);
}

	
function appm_nxtMonth(month, year){
	if(month != 11) appm_calendarGenerate(month + 1, year);
	else appm_calendarGenerate(0, year + 1);
}

function appd_prevDate(date, month, year){
	var dateChange = date;
	var monthChange = month;
	var yearChange = year;
	
	if(date != 1){
		loadcupidapoint(date - 1, month, year);
		dateChange = date - 1;
	}
	else {
		if(month != 0){
			var monthLength = cal_days_in_month[month - 1];
			if (month == 1) { // February only
			  if ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0){
				monthLength = 29;
			  }
			}
			loadcupidapoint(monthLength, month - 1, year);
			dateChange = monthLength;
			monthChange = month - 1;
		}
		else {
			loadcupidapoint(31, 11, year - 1);
			dateChange = 31;
			monthChange = 11;
			yearChange = year - 1;
		}
	}
}

function appd_nxtDate(date, month, year){
	var dateChange = date;
	var monthChange = month;
	var yearChange = year;
	
	var monthLength = cal_days_in_month[month];
	if (month == 1) { // February only
	  if ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0){
		monthLength = 29;
	  }
	}
	if(date != monthLength){
		loadcupidapoint(date + 1, month, year);
		dateChange = date + 1;
	}
	else {
		if(month != 11){
			loadcupidapoint(1, month + 1, year);
			dateChange = 1;
			monthChange = month + 1;
		}
		else {
			loadcupidapoint(1, 0, year + 1);
			dateChange = 1;
			monthChange = 0;
			yearChange = year + 1;
		}
	}
}

