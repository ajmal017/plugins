jQuery(document).ready(function($){

	jQuery('.all-inv-tran-rec').DataTable({
  "columns": [
    { "width": "20%" },
    { "width": "10%" },
    { "width": "5%" },
    { "width": "10%" },
    { "width": "10%" },
    { "width": "10%" },
    { "width": "5%" },
    { "width": "5%" },
    { "width": "10%" },
    { "width": "10%" },
    { "width": "5%" }
  ]
});

	/* select 2 library for accounts */
	jQuery("select[name='account-list']").select2({
	  placeholder: "-Select Account-",
	  allowClear: true
	});

	/* Account create functionality start */
	jQuery('#create-btn').click(function(){
		
		var accountName 	= jQuery('.create-account').find('.account-name').val();
		var recordingCurr 	= jQuery('.create-account').find('.rec-curr').val();
		var description 	= jQuery('.create-account').find('.description').val();
		var userID 			= jQuery('.create-account').find('.userId').val();
		var createdDate		= jQuery('.create-account').find('.createdDate').val();
		var url				= jQuery('.create-account').find('.url').val();
		
		if (accountName != '' && recordingCurr != '0' ) {
			jQuery('.loader').show();
			jQuery.ajax({
	            type:"POST",
	            url: url,
	            data: {action:'create_account_request', accountName: accountName,recordingCurr: recordingCurr,description: description,userID: userID,createdDate: createdDate },
	            success:function(data){
	            	jQuery('.loader').hide();
	                if(data == 'accountcreated'){
	                	//currentRow.remove();
	                	jQuery('.create-account').find('.account-name').val('');
						jQuery('.create-account').find('.rec-curr').val('0');
						jQuery('.create-account').find('.description').val('');
						
	                	jQuery(".account .success").fadeIn(3000).fadeOut(3000);
	                }
	            }
	        });
		}else{
			jQuery('.account').find('.errors').fadeIn(3000).fadeOut(3000);
		}
	});
	/* Account create functionality end */

	/* Account update functionality start */
	jQuery('#update-btn').click(function(){
		var accountName 	= jQuery('.update-account').find('.account-name').val();
		var recordingCurr 	= jQuery('.update-account').find('.rec-curr').val();
		var description 	= jQuery('.update-account').find('.description').val();
		var userID 			= jQuery('.update-account').find('.userId').val();
		var url				= jQuery('.update-account').find('.url').val();
		var id				= jQuery('.update-account').find('.id').val();

		if (accountName != '' && recordingCurr != '0' ) {
			jQuery('.loader').show();
			jQuery.ajax({
	            type:"POST",
	            url: url,
	            data: {action:'update_account_request', accountName: accountName,recordingCurr: recordingCurr,description: description,userID: userID,id: id },
	            success:function(data){
	            	jQuery('.loader').hide();
	                if(data == 'accountupdate'){
	                	jQuery(".account-update .success").fadeIn(3000).fadeOut(3000);
	                }
	            }
	        });
		}else{
			jQuery('.account-update').find('.errors').fadeIn(3000).fadeOut(3000);
		}
	});
	/* Account update functionality end */

	/* Account delete functionality */
	jQuery('.trash').click(function(){
		var id = jQuery(this).attr('id');
		jQuery('.loader').show();
		var url		= jQuery('.create-account').find('.url').val();
		var row 	= jQuery(this).parent().parent();
		console.log(row);	
		jQuery.ajax({
            type:"POST",
            url: url,
            data: {action:'delete_account_request', id: id },
            success:function(data){
            	jQuery('.loader').hide();
                if(data == 'accountdeleted'){
                	row.remove();
                	jQuery(".delete-account").fadeIn(3000).fadeOut(3000);
                }
            }
        });
	});

	/* Investment Transaction Record create functionality start */
	jQuery('#save-investment').click(function(){
		
		//var userID 			= jQuery('.create-investment').find('.userId').val();
		var url				= jQuery('.create-investment').find('.url').val();
		var type			= jQuery('.create-investment').find(".top-cols-1").find('#buy-sell:checked').val();
		var tradeDate		= jQuery('.create-investment').find(".top-cols-2").find('.tradeDate').val();
		var settleDate		= jQuery('.create-investment').find(".top-cols-3").find('.settleDate').val();
		var trading_curr	= jQuery('.create-investment').find('.rec-tra-curr').val();
		var equity			= jQuery('.create-investment').find('.equity').val();
		var ticker_symbol	= jQuery('.create-investment').find('.ticker-symbol').val();
		var shares			= jQuery('.create-investment').find('.shares').val();
		var price			= jQuery('.create-investment').find('.transaction-price').val();
		var fees			= jQuery('.create-investment').find('.transaction-fees').val();
		var broker			= jQuery('.create-investment').find('.broker').val();
		var accountName		= jQuery('.create-investment').find('.account-name').val();
		var notes			= jQuery('.create-investment').find('.notes').val();

		/* validation for numeric values */
		var flag =1;
		var falseValue = [];
		
		if (isNaN(shares) || shares < 0 || shares=='') {
        	jQuery(".num_error").show();
        	falseValue.push("0");
	    }else{
	    	jQuery(".num_error").hide();
	    }

	    if (isNaN(price) ||  price=='') {
        	jQuery(".price_error").show();
        	falseValue.push("0");
	    }else{
	    	jQuery(".price_error").hide();
	    }

	    if (isNaN(fees) ||  fees=='') {
        	jQuery(".fees_error").show();
        	falseValue.push("0");
	    }else{
	    	jQuery(".fees_error").hide();
	    }

	    if (isNaN(trading_curr) ||  trading_curr=='') {
	    	jQuery(".rate_error").show();
	    	falseValue.push("0");
	    }else{
	    	jQuery(".rate_error").hide();
	    }
		var validData = jQuery.inArray("0", falseValue);
		if (validData >= 0) {
			jQuery('.investment-transaction').find('.errors').fadeIn(3000).fadeOut(4000);
		}else{

			if(accountName != '' && ticker_symbol != '' && equity != '' && broker != '' && type != '' && tradeDate != '' && settleDate != '' ) {
			
				jQuery('.loader').show();
				jQuery.ajax({
		            type:"POST",
		            url: url,
		            data: {
		            		action:'create_investment_transaction_record_request', 
				            //userID: userID,
				            url: url, 
				            type: type, 
				            tradeDate: tradeDate, 
				            settleDate: settleDate, 
				            trading_curr: trading_curr, 
				            equity: equity, 
				            ticker_symbol: ticker_symbol, 
				            shares: shares, 
				            price: price, 
				            fees: fees, 
				            broker: broker, 
				            account: accountName, 
				            notes: notes, 
				        },
		            success:function(data){
		            	console.log(data);
		            	jQuery('.loader').hide();

		            	goToByScroll("success");

		                if(data == 'investmenttransactioncreated'){
		                	jQuery(".investment-transaction .success").fadeIn(3000);
		                }else{
		                	jQuery('.investment-transaction').find('.errors').fadeIn(3000);
		                }

		                location.reload(true);
		            }
		        });
		    }else{
		    	goToByScroll("errors");
		    	jQuery('.investment-transaction').find('.errors').fadeIn(3000).fadeOut(4000);
		    }
		}
	});
	/* Investment Transaction Record create functionality end */


	function goToByScroll(id){
	    
	    var scrollTop     = jQuery(window).scrollTop(),
	    elementOffset = jQuery('.'+id).offset().top,
	    distance      = (elementOffset - scrollTop);

	    jQuery(window).scrollTop(distance);
	}

	/* Investment transaction record update functionality start */
	jQuery('#update-investment').click(function(){
		var userID 			= jQuery('.update-investment').find('.userId').val();
		var id				= jQuery('.update-investment').find('.id').val();
		var url				= jQuery('.update-investment').find('.url').val();
		var type			= jQuery('.update-investment').find('#buy-sell:checked').val();
		var tradeDate		= jQuery('.update-investment').find('.tradeDate').val();
		var settleDate		= jQuery('.update-investment').find('.settleDate').val();
		//var trading_curr	= jQuery('.update-investment').find('.trading-curr').val();
		var equity			= jQuery('.update-investment').find('.equity').val();
		var ticker_symbol	= jQuery('.update-investment').find('.ticker-symbol').val();
		var shares			= jQuery('.update-investment').find('.shares').val();
		var price			= jQuery('.update-investment').find('.transaction-price').val();
		var fees			= jQuery('.update-investment').find('.transaction-fees').val();
		var broker			= jQuery('.update-investment').find('.broker').val();
		var accountName		= jQuery('.update-investment').find('.account-name').val();
		var notes			= jQuery('.update-investment').find('.notes').val();

		jQuery('.loader').show();
		jQuery.ajax({
            type:"POST",
            url: url,
            data: {
            		action:'update_investment_transaction_record_request', 
		            userID: userID,
		            url: url, 
		            type: type, 
		            tradeDate: tradeDate, 
		            settleDate: settleDate, 
		            //trading_curr: trading_curr, 
		            equity: equity, 
		            ticker_symbol: ticker_symbol, 
		            shares: shares, 
		            price: price, 
		            fees: fees, 
		            broker: broker, 
		            account: accountName, 
		            notes: notes, 
		            id: id, 
		        },
            success:function(data){
            	console.log(data);
            	jQuery('.loader').hide();
                if(data == 'investmenttransactionupdated'){
                	jQuery(".investment-update .success").fadeIn(3000).fadeOut(3000);
                }else{
                	jQuery('.investment-update').find('.errors').fadeIn(3000).fadeOut(3000);
                }
            }
        });
	});
	/* Investment transaction record update functionality end */
	
	/* Investment transaction delete functionality */
	jQuery('.trash-investment').click(function(){
		var id = jQuery(this).attr('id');
		jQuery('.loader').show();
		var url		= jQuery('.create-investment').find('.url').val();
		var row 	= jQuery(this).parent().parent();
		console.log(row);	
		jQuery.ajax({
            type:"POST",
            url: url,
            data: {action:'delete_investment_transaction_request', id: id },
            success:function(data){
            	jQuery('.loader').hide();
                if(data == 'investmenttransactiondeleted'){
                	row.remove();
                	jQuery(".delete-account").fadeIn(3000).fadeOut(3000);
                }
            }
        });
	});


	function getConversionRate(){
		var ticker 	= jQuery(".ticker-symbol").val();
		var account = jQuery(".account-name").val();
		var tradeDate = jQuery("input[name='tradeDate']").val();

		var url		= jQuery('input[name="url"]').val(); 

		if(ticker != "" && account != "" && tradeDate != "" ){

			jQuery.ajax({
	            type:"POST",
	            url: url,
	            data: {action:'get_conversion_rate', ticker: ticker, account:account, tradeDate:tradeDate },
	            success:function(data){

	            	jQuery("input[name='rec-tra-curr']").val(data);	            	
	            	
	            }
	        });

		}
	}

	jQuery("input[name='tradeDate']").change(function(){

		getConversionRate();

	});

	jQuery(".account-name").change(function(){

		getConversionRate();

	});

	jQuery(".ticker-symbol").blur(function(){

		getConversionRate();

	});


	/* fill other field base on ticker symbol on record page */
	jQuery(".ticker-symbol, .account-name").change(function(){ 
		var ticker 	= jQuery(".ticker-symbol").val();
		var account = jQuery(".account-name").val();
		
		if(ticker != "" && account != ""){
			var url		= jQuery('.create-investment').find('.url').val(); 
			
			jQuery.ajax({
	            type:"POST",
	            url: url,
	            data: {action:'fill_other_fields', ticker: ticker, accountID:account },
	            success:function(data){
	            	var result 				= JSON.parse (data);
	            	var recording_currency 	= result.recording_currency;
	            	var trade_currency 		= result.trade_currency;
	            	var equity 				= result.stock_name;
	            	var exchangeRate 		= "Exchange rate - "+recording_currency+"/"+trade_currency;
	            	
	            	jQuery(".rec-tra-curr").parent().prev().find("span").text(exchangeRate);
	            	jQuery(".equity").val(equity);
	            	jQuery(".transaction-price").parent().prev().find("span").text("Transaction price ("+trade_currency+")");
	            	jQuery(".transaction-fees").parent().prev().find("span").text("Transaction fees ("+trade_currency+")");
	            }
	        });
		}

	});
	
	jQuery(".date-ranger").find(".date1").datepicker({
		 dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true
	});

	jQuery(".datepicker").datepicker({
		 dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true
	});


	jQuery(".reporting-portfolio-dates").find(".reporting-date-start").datepicker({
		 dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true
	});
	jQuery(".reporting-portfolio-dates").find(".reporting-date-end").datepicker({
		 dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true
	});
	
	
	/* change show entries text */
	//var selectHtml 	= jQuery(".dataTables_length").find("label").html();
	//var replaceWord	= selectHtml.replace("entries","most recent entries");
	//jQuery(".dataTables_length label").html(replaceWord);

	/* Reporting Portfolio value */


	function setData(startDate, userId, url){
		
		jQuery.ajax({
            type:"POST",
            url: url,
            data: {action:'reporting_portfolio_value_request', startDate:startDate,userId:userId },
            beforeSend:function(){
            	jQuery('.loader').show();
            },
            success:function(data){

            	var result 				= JSON.parse (data);

            	var html = "";

            	jQuery(result.items).each(function(index, element){

            		html += "<tr>";
            		html += 	"<td>"+ element.account_name +"</td>";
            		html += 	"<td>"+ element.company_name +"</td>";            		
            		html += 	"<td>"+ element.ticker_symbol +"</td>";
            		html += 	"<td>"+ element.total_shares +"</td>";
            		html += 	"<td>"+ element.price +"</td>";
            		html += 	"<td>"+ element.total_values +"</td>";            		
            		html += "</tr>";

            	});

            	/*jQuery(result.total).each(function(index, element){

            		html += "<tr>";
            		html += 	"<td></td>";
            		html += 	"<td></td>";            		
            		html += 	"<td><b>Total</b></td>";
            		html += 	"<td><b>"+ element.grand_total_shares +"</b></td>";
            		html += 	"<td><b>"+ element.grand_total_price +"</b></td>";
            		html += 	"<td><b>"+ element.grand_total_value +"</b></td>";            		
            		html += "</tr>";

            	});*/

            	jQuery(".reporting-portfolio-value tbody").html(html);

            },
            complete:function(){
            	jQuery('.loader').hide();	
            }
        });		
	}

	//jQuery(".ticker-by-date").click(function(){
	jQuery(".date1").change(function(){
		var startDate 	= jQuery('.date1').val();
		var url			= jQuery('.reporting-portfolio').find('.url').find(".wp_url").val(); 
		var userId		= jQuery('.reporting-portfolio').find('.url').find(".userId").val(); 
		if (startDate != '') {
			setData(startDate, userId, url);
		}
	});

	var currentDate = new Date();
	var changeDate 	= currentDate.getFullYear()+'-'+(currentDate.getMonth()+1) + '-' + currentDate.getDate();
	var url			= jQuery('.reporting-portfolio').find('.url').find(".wp_url").val(); 
	var userId		= jQuery('.reporting-portfolio').find('.url').find(".userId").val(); 
	var reportPage	= jQuery('.reporting-portfolio').find('.url').find(".pageURL").val(); 
	var currentPage = window.location.href;
	var reportURL   = reportPage+"/index.php/report/";

	if (reportURL == currentPage) {
		setData(changeDate, userId, url);
		
		var date2 	= currentDate.getFullYear() + '-' + formatDateMonth( currentDate.getMonth()+1 ) + '-' + currentDate.getDate();
		
		jQuery(".date1").val(date2);
	}



	//function to show chart on report page...

	function showChart(time_date, reporting_date_start, reporting_date_end){

		// initialize color codes array...
		color_codes = new Array();

		jQuery('input[name="reporting-date-start"]').val(reporting_date_start);

		jQuery('input[name="reporting-date-end"]').val(reporting_date_end);

		var url	= jQuery('input[name="url"]').val();
		
		if(reporting_date_start != '' && reporting_date_end != ""){

			var ajax_data;

			jQuery.ajax({
	            type:"POST",
	            url: url,
	            async: true,
	            data: {action:'chart_portfolio', reporting_date_start: reporting_date_start,reporting_date_end: reporting_date_end,time_date: time_date },
	            beforeSend: function(){
	            	jQuery('.loader').show();
	            },
	            success:function(res){	            	
	                ajax_data = JSON.parse(res);

	                google.charts.load('current', {packages: ['corechart', 'line']});
					google.charts.setOnLoadCallback(drawLogScales);
					
	            },
	            complete:function(){
	            	jQuery('.loader').hide();	
	            }
	        });

			/*google.charts.load('current', {packages: ['corechart', 'line']});
			google.charts.setOnLoadCallback(drawLogScales);*/

			function drawLogScales() {
			    var data = new google.visualization.DataTable();
			    data.addColumn('date', 'X');

			    jQuery( ajax_data.accounts ).each(function( index, element ) {

			    	data.addColumn('number', element.name);

			    });

			    jQuery( ajax_data.data ).each(function( index, element ) {

			    	var obj_length = Object.keys(element).length;

			    	date_parts = element.date.split("-");

			    	var item = new Array();

			    	item.push(new Date(date_parts[0], date_parts[1]-1, date_parts[2]));

			    	jQuery.each( element, function(key, value) {

			    		if(key !== "date"){			    			
			    			item.push( parseFloat(value) );
			    		}
				    });

			    	data.addRow(item);

				});

			    // get color codes randomly
			    getRandomColorCode(ajax_data.accounts.length);

				console.log(color_codes);

			    var options = {
			    	title: 'Portfolio Chart',
			    	'height':400,
			        hAxis: {
			          title: 'Time',
			          logScale: false
			        },
			        vAxis: {
			          title: 'Reporting value',
			          logScale: false
			        },			        
			        colors: color_codes,
			        legend: { position: 'bottom' },
			        backgroundColor: '#f1f8e9'
			    };

		      	var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
		      	chart.draw(data, options);
		    }

		}else{
			alert("Please select dates!");
		}
	}

	function showSingleChart(time_date, reporting_date_start, reporting_date_end, accountList){

		// initialize color codes array...
		color_codes = new Array();

		jQuery('input[name="reporting-date-start"]').val(reporting_date_start);

		jQuery('input[name="reporting-date-end"]').val(reporting_date_end);

		var url	= jQuery('input[name="url"]').val();
		
		if(reporting_date_start != '' && reporting_date_end != ""){

			var ajax_data;

			jQuery.ajax({
	            type:"POST",
	            url: url,
	            async: true,
	            data: {action:'single_chart_portfolio', reporting_date_start: reporting_date_start,reporting_date_end: reporting_date_end,time_date: time_date,accountList:accountList },
	            beforeSend: function(){
	            	jQuery('.loader').show();
	            },
	            success:function(res){	            	
	                ajax_data = JSON.parse(res);
	                if (ajax_data.accounts == "") {
	                	jQuery('.loader').hide();
	                	jQuery(".account-empty h4").fadeIn(4000).fadeOut(4000);
	                	/*window.location.reload();
	                	jQuery("select[name='account-list']").val('').change();	*/
	            	}
	                google.charts.load('current', {packages: ['corechart', 'line']});
					google.charts.setOnLoadCallback(drawLogScales);
					
	            },
	            complete:function(){
	            	jQuery('.loader').hide();	
	            }
	        });

		
			function drawLogScales() {
			    var data = new google.visualization.DataTable();
			    data.addColumn('date', 'X');

			    jQuery( ajax_data.accounts ).each(function( index, element ) {

			    	data.addColumn('number', element.name);

			    });

			    jQuery( ajax_data.data ).each(function( index, element ) {

			    	var obj_length = Object.keys(element).length;

			    	date_parts = element.date.split("-");

			    	var item = new Array();

			    	item.push(new Date(date_parts[0], date_parts[1]-1, date_parts[2]));

			    	jQuery.each( element, function(key, value) {

			    		if(key !== "date"){			    			
			    			item.push( parseFloat(value) );
			    		}
				    });

			    	data.addRow(item);

				});

			    // get color codes randomly
			    getRandomColorCode(ajax_data.accounts.length);

				console.log(color_codes);

			    var options = {
			    	title: 'Portfolio Chart',
			    	'height':400,
			        hAxis: {
			          title: 'Time',
			          logScale: false
			        },
			        vAxis: {
			          title: 'Reporting value',
			          logScale: false
			        },			        
			        colors: color_codes,
			        legend: { position: 'bottom' },
			        backgroundColor: '#f1f8e9'
			    };

		      	var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
		      	chart.draw(data, options);
		    }

		}else{
			alert("Please select dates!");
		}
	}

	function formatDateMonth (dateComponent) {
	  return (dateComponent < 10 ? '0' : '') + dateComponent;
	}

	var formatDateComponent = function(dateComponent) {
	  return (dateComponent < 10 ? '0' : '') + dateComponent;
	}

	var formatDate = function(date) {	  
		return date.getFullYear() + '-' + formatDateComponent(date.getMonth() + 1) + '-' + formatDateComponent(date.getDate());	  
	}

	var color_codes = new Array();
	var tmp_color_codes = new Array();

	function getRandomColorCode(colorNums){
		  
		var colorCodePre = ['FF0000', '0000FF', '4D0000', '000080', 'FF0066', '990033', '9933FF', '994D00', 'B300B3', '003D99', '80002A', '800000'];

		var colorCodeRandom = colorCodePre[Math.floor(Math.random() * colorCodePre.length)];

		if( tmp_color_codes.indexOf(colorCodeRandom) >= 0 ){
			getRandomColorCode(colorNums);
		}else if(color_codes.length < colorNums){
			color_codes.push('#'+ colorCodeRandom);

			tmp_color_codes.push(colorCodeRandom);

			getRandomColorCode(colorNums);
		}else{
			tmp_color_codes = new Array();
		}	  
	}

	function getLastMonthDates(){

		var now = new Date();
		var prevMonthLastDate = new Date(now.getFullYear(), now.getMonth(), 0);
		var prevMonthFirstDate = new Date(now.getFullYear(), (now.getMonth() - 1 + 12) % 12, 1);

		var dates = new Array();

		dates.push( formatDate(prevMonthFirstDate) );

		dates.push( formatDate(prevMonthLastDate) );		

		return dates;		
	}

	var dates = getLastMonthDates();

	showChart(1, dates[0], dates[1]);

	/* google chart for reporting portfolio */

	jQuery("input[name='reporting-date-start'], input[name='reporting-date-end']").on("change", function(){

		setTimeDropdown();

	});


	function setTimeDropdown(){

		var reporting_date_start = jQuery("input[name='reporting-date-start']").val();
		
		var reporting_date_end = jQuery("input[name='reporting-date-end']").val(); 

		var start = new Date(reporting_date_start),
	    end   = new Date(reporting_date_end),
	    diff  = new Date(end - start),
	    days  = diff/1000/60/60/24;
	    
	    if (days <= 31) {	    	
	    	var html = '<option value="1" style="">Daily</option>';
	    	jQuery("select[name='time-date']").html(html);

	    }else if(days >=31 && days <= 92){
	    	
	    	var html = '<option value="7" style="">Weekly</option>';
	    	jQuery("select[name='time-date']").html(html);
	    	
	    }else if(days >92){
	    	
	    	var html = '<option value="30">Monthly</option><option value="90">Quarterly</option>';
	    	jQuery("select[name='time-date']").html(html);	    	
	    }
	}

	/* account chart using select 2 */
	/*jQuery("select[name='account-list']").on("change", function(){
		var time_date = jQuery("select[name='time-date']").val();
		
		var reporting_date_start = jQuery("input[name='reporting-date-start']").val();
		
		var reporting_date_end = jQuery("input[name='reporting-date-end']").val(); 		

		var accountList = 	jQuery("select[name='account-list']").val();

		showSingleChart(time_date, reporting_date_start, reporting_date_end, accountList);
		//showChart(time_date, reporting_date_start, reporting_date_end);

	});*/

	jQuery(".reporting-submit").click(function(){

		var time_date = jQuery("select[name='time-date']").val();
		
		var reporting_date_start = jQuery("input[name='reporting-date-start']").val();
		
		var reporting_date_end = jQuery("input[name='reporting-date-end']").val(); 	

		var accountList = 	jQuery("select[name='account-list']").val();
		if (accountList != "") {
			showSingleChart(time_date, reporting_date_start, reporting_date_end, accountList);
		}else{
			showChart(time_date, reporting_date_start, reporting_date_end);
		}	


	});

	function getTotalReturns(start_date, end_date){

		var url = jQuery("input[name='url']").val();

		jQuery.ajax({
			type:"POST",
			url: url,
			async: false,
			data: {action:'get_total_returns', start_date:start_date, end_date:end_date },
			beforeSend: function(xhr){
				console.log(xhr);
				jQuery('.loader').show();
			},
			success: function(result){

				var data = JSON.parse(result);
				
				var html = "";

				jQuery(data).each(function(index, element){

					var t_type = (element.transaction_type==1)?'Buy':'Sell';

					html += "<tr>";
            		html += 	"<td>"+ element.company_name +"</td>";
            		html += 	"<td>"+ element.ticker_symbol +"</td>";            		
            		html += 	"<td>"+ element.num_of_shares +"</td>";
            		html += 	"<td>"+ element.trade_date +"</td>";
            		html += 	"<td>"+ t_type +"</td>";
            		html += 	"<td>"+ element.total_report_curr_price +"</td>";
            		html += 	"<td>"+ element.total_value +"</td>";
            		html += 	"<td>"+ element.dividend_income +"</td>";            		
            		html += "</tr>";

				});

				console.log(html);

				jQuery("#tbl_total_returns tbody").html(html);

			},
			complete: function(){
				jQuery('.loader').hide();
			},
			error: function(){
				jQuery('.loader').hide();	
			}
		});
	}


	jQuery("input[name='get_total_returns']").on("click", function(){

		var start_date = jQuery("input[name='start_date']").val();
		var end_date = jQuery("input[name='end_date']").val();

		if( start_date != "" && end_date != "" ){

			getTotalReturns(start_date, end_date);

		}else{
			alert("Please select the period of interest!");
		}
	});






});