jQuery(document).ready(function($){

	/* Stock ajax functionality start */
	var   stockUrl 			= $(".stockurl").val();
	//var   stockuploader = $('.stockuploader').val();
	$('.stockfile').on('change',prepareUploadStock);		

	function prepareUploadStock(event) { 
		var file = event.target.files;
	  	var parent = $("#" + event.target.id).parent();
	  	var data = new FormData();
	  	data.append("action", "uploadStockCSV");
	  	$.each(file, function(key, value){
	      		data.append("uploadStockCSV", value);
	    });
	  	jQuery('.loader').show();
    	$.ajax({
		  	url: stockUrl,
          	type: 'POST',
          	data: data,
          	cache: false,
          	dataType: 'json',
          	processData: false, 
          	contentType: false, 
          	success: function(data) {
          		
          		if (data.records.length > 0 ) {
          			
					var theadtr = "<tr><th colspan='3'>Following duplicate record(s) have been found while processing CSV.</th></tr><tr><th>Stock Name</th><th>Ticker</th><th>Exchange Name</th></tr>";
					jQuery('.duplicates').find('table').find('thead').append(theadtr);
					jQuery('.duplicates').css('border','2px solid #666');

          			for(i=0;i<data.record.length;i++) { 
						var stockName = data.record[i][0].stock_name;
						var ticker 	  = data.record[i][0].ticker;
						var exchange  = data.record[i][0].exchange_name;
						var tbody = "<tr><td>"+stockName+"</td><td>"+ticker+"</td><td>"+exchange+"</td></tr>";
						jQuery('.duplicates').find('table').find('tbody').append(tbody);
					}
          		}
          		
          		jQuery('.loader').hide();
          		if (data.stocksuccess == 'stocksuccess') {
          			location.reload();
          			jQuery(".success p").fadeIn(1000);
          		}else{
          			jQuery(".errors p").fadeIn(3000).fadeOut(3000);
          		}
	  		}
		});
	}
	/* Stock ajax functionality end */

	/* Stock price ajax functionality start */
	var   stockPriceUrl 	= $(".stockpriceurl").val();
	$('.stockpricefile').on('change',prepareUploadStockPrice);		

	function prepareUploadStockPrice(event) { 
		var file = event.target.files;
	  	var parent = $("#" + event.target.id).parent();
	  	var data = new FormData();
	  	data.append("action", "uploadStockPriceCSV");
	  	$.each(file, function(key, value){
	      		data.append("uploadStockPriceCSV", value);
	    });
	  	jQuery('.loader').show();
    	$.ajax({
		  	url: stockPriceUrl,
          	type: 'POST',
          	data: data,
          	cache: false,
          	dataType: 'json',
          	processData: false, 
          	contentType: false, 
          	success: function(data) {
          		jQuery('.loader').hide();
          		if (data.stockpricesuccess == 'stockpricesuccess') {
          			location.reload();
          			jQuery(".success p").fadeIn(1000);
          		}else{
          			jQuery(".errors p").fadeIn(3000).fadeOut(3000);
          		}
	  		}
		});
	}
	/* Stock price ajax functionality end */

	/* Dividend ajax functionality start */
	var   dividendUrl 	= $(".dividendurl").val();
	
	$('.dividendfile').on('change',prepareUploadDividend);		

	function prepareUploadDividend(event) { 
		var file = event.target.files;
	  	var parent = $("#" + event.target.id).parent();
	  	var data = new FormData();
	  	data.append("action", "uploadDividendCSV");
	  	$.each(file, function(key, value){
	      		data.append("uploadDividendCSV", value);
	    });
	  	jQuery('.loader').show();
    	$.ajax({
		  	url: dividendUrl,
          	type: 'POST',
          	data: data,
          	cache: false,
          	dataType: 'json',
          	processData: false, 
          	contentType: false, 
          	success: function(data) {
          		
          		if (data.records.length > 0 ) {
          			
					var theadtr = "<tr><th colspan='3'>Following duplicate record(s) have been found while processing CSV.</th></tr><tr><th>CompanyName</th><th>Ex_Date</th><th>Annc_Type</th><th>Interest Start</th><th>Interest End</th></tr>";
					jQuery('.duplicates').find('table').find('thead').append(theadtr);
					jQuery('.duplicates').css('border','2px solid #666');
          			
          			for(i=0;i<data.records.length;i++) { 

						var CompanyName             = data.records[i][0].company_name;
						var Ex_Date                 = data.records[i][0].ex_date;
						var Annc_Type               = data.records[i][0].annc_type;
						var interest_start          = data.records[i][0].interest_start;
						var interest_end            = data.records[i][0].interest_end;
						var tbody = "<tr><td>"+CompanyName+"</td><td>"+Ex_Date+"</td><td>"+Annc_Type+"</td><td>"+interest_start+"</td><td>"+interest_end+"</td></tr>";
						jQuery('.duplicates').find('table').find('tbody').append(tbody);
					}
          		}

          		jQuery('.loader').hide();
          		
          		if (data.dividendsuccess == 'dividendsuccess') {
          			location.reload();
          			jQuery(".success p").fadeIn(1000);
          		}else{
          			jQuery(".errors p").fadeIn(3000).fadeOut(3000);
          		}
	  		}
		});
	}
	/* Dividend ajax functionality end */

	/* Exchange-Rate ajax functionality start */
	var   exchangeUrl 	= $(".exchangeurl").val();
	
	$('.exchangefile').on('change',prepareUploadExchange);		

	function prepareUploadExchange(event) { 
		var file = event.target.files;
	  	var parent = $("#" + event.target.id).parent();
	  	var data = new FormData();
	  	data.append("action", "uploadExchangeCSV");
	  	$.each(file, function(key, value){
	      		data.append("uploadExchangeCSV", value);
	    });
	  	jQuery('.loader').show();
    	$.ajax({
		  	url: exchangeUrl,
          	type: 'POST',
          	data: data,
          	cache: false,
          	dataType: 'json',
          	processData: false, 
          	contentType: false, 
          	success: function(data) {
          		
          		if (data.records.length > 0 ) {
          			
					var theadtr = "<tr><th colspan='4'>Following duplicate record(s) have been found while processing CSV.</th></tr><tr><th>Date</th><th>Base Currency</th><th>Conversion Currency</th><th>Value</th></tr>";
					jQuery('.duplicates').find('table').find('thead').append(theadtr);
					jQuery('.duplicates').css('border','2px solid #666');
          			for(i=0;i<data.record.length;i++) { 
						
						var date 				= data.record[i][0].date;
						var base_currency 		= data.record[i][0].base_currency;
						var conversion_currency = data.record[i][0].conversion_currency;
						var value  				= data.record[i][0].value;

						var tbody = "<tr><td>"+date+"</td><td>"+base_currency+"</td><td>"+conversion_currency+"</td><td>"+value+"</td></tr>";
						jQuery('.duplicates').find('table').find('tbody').append(tbody);
					}
          		}
          		jQuery('.loader').hide();
          		if (data.exchangesuccess == 'exchangesuccess') {
          			location.reload();
          			jQuery(".success p").fadeIn(1000);
          		}else{
          			jQuery(".errors p").fadeIn(3000).fadeOut(3000);
          		}
	  		}
		});
	}
	/* Exchange-Rate ajax functionality end */

	/* Stock delete ajax functionality start */
	jQuery('.stock-del').click(function(){
		var stockId = jQuery(this).val();
		var currentRow = jQuery(this).parent().parent();
		jQuery.ajax({
            type:"POST",
            url: stockUrl,
            data: {action:'stock_delete_request', stock_id: stockId },
            success:function(data){
            	console.log(data);
                if(data == 'stockdeleted'){
                	currentRow.remove();
                	jQuery(".delMessage p").fadeIn(3000).fadeOut(3000);
                }
            }
        });

	});
	/* Stock delete ajax functionality end */

	/* Dividend delete ajax functionality start */
	jQuery('.dividend-del').click(function(){
		
		var dividendId = jQuery(this).val();
		var currentRow = jQuery(this).parent().parent();
		jQuery.ajax({
            type:"POST",
            url: dividendUrl,
            data: {action:'dividend_delete_request', dividend_id: dividendId },
            success:function(data){
            	console.log(data);
                if(data == 'dividenddeleted'){
                	currentRow.remove();
                	jQuery(".delMessage p").fadeIn(3000).fadeOut(3000);
                }
            }
        });

	});
	/* Dividend delete ajax functionality end */

	/* Exchange delete ajax functionality start */
	jQuery('.exchange-del').click(function(){
		
		var exchangeID = jQuery(this).val();
		var currentRow = jQuery(this).parent().parent();
		jQuery.ajax({
            type:"POST",
            url: exchangeUrl,
            data: {action:'exchangeRate_delete_request', id: exchangeID },
            success:function(data){
            	console.log(data);
                if(data == 'exchangeRatedeleted'){
                	currentRow.remove();
                	jQuery(".delMessage p").fadeIn(3000).fadeOut(3000);
                }
            }
        });

	});
	/* Exchange delete ajax functionality end */

	/* Dividend setting ajax functionality start */
	jQuery('.dividendChecklistBtn').click(function(){
		var   settingUrl 	= $(".dividend-hidden").val();
		
		var arr = jQuery('.dividendchecklist:checked').map(function(){
			return jQuery(this).val();
		}).get();

        console.log(arr);
		jQuery.ajax({
            type:"POST",
            url: settingUrl,
            data: {action:'dividend_setting_request', data: arr },
            success:function(data){
            	if(data == 'dividendsettingsave'){
                	jQuery(".success p").fadeIn(3000).fadeOut(3000);
                }
            }
        });
	});

});