window.onclick = function(event) {
	jQuery( ".wps-inline-modal" ).each(function( index ) {
		var modalId = jQuery(this).attr('id');								 
		modal = document.getElementById(modalId);
		if (event.target == modal)
		{
			//modal.style.display = "none";
		}						 
	});
}
/**
 *
 * Task Plugin which contain most common function 
 * @uses	: use function from this Task object 
 *
 **/
var Task = (function($,w){
	var task = function(){
		this.params = {
			loaderStatus:null,
			referrer : null,
			loaderWrapper : 'body',
			xhr : null
		}
	}
	task.prototype = {
		init:function(){
			var self = this;
		},
		showLoader:function(options){
			var defaults = $.extend({wrapper:'body'},options);
			this.params.loaderWrapper = defaults.wrapper;
			var loadder = '<div id="loading">';
			if(defaults.wrapper=='body'){
				loadder += '<div class="wps-modal-backdrop fade show"></div>'+'<div class="wpsOverlay"><i class="fa fa-spinner fa-spin fa-5x"></i><p>LOADING . . .</p></div>';
			}else{
				loadder += '<div class="wps-modal-backdrop fade show position-absolute"></div>'+'<div class="wpsOverlay position-absolute"><i class="fa fa-spinner fa-spin fa-5x"></i></div>';
			}
			$(defaults.wrapper).find('#loading').remove();
			$(defaults.wrapper).append(loadder);
		},
		hideLoader:function(){
			$(this.params.loaderWrapper).find('#loading').remove();
		},
		fillCanvas:function(config){
			var defaults = $.extend({canvasId:'canvas',imageId:'image'},config);
			alert(defaults.canvasId+"  "+defaults.imageId);
			var canvas = $('#'+defaults.canvasId);
            var ctx = $('#'+defaults.canvasId).getContext('2d');
            var img = $('#'+defaults.imageId);

            canvas.width = img.width;
            canvas.height = img.height;
            alert(img.width);
            alert(img.height);
            ctx.drawImage(img, 0, 0);  
            return true;

		},
		callAjax:function(config){
			var defaults = $.extend({onComplete:false,uploadImg:false},config);
			
			if(this.params.xhr && this.params.xhr.readyState != 4){
	            this.params.xhr.abort();
	        }
	        if(defaults.uploadImg){
	        	this.params.xhr = $.ajax({url:defaults.url,
	        						  data:defaults.params,
	        						  type:'post',
	        						  contentType: false,
	            					  processData: false,
	            					  success:function(response){
										var res = $.parseJSON(response);
										if(res.redirect){
							            	window.location.href = res.redirect;
							            	return false;
							            }
									  	if(typeof defaults.onComplete == 'string'){
									  		eval(defaults.onComplete);
									  	}else if(typeof defaults.onComplete == 'function'){
									  		defaults.onComplete.call(this,response);
									  	}
									  }
									});
	    	}else{
	    		this.params.xhr = $.post(defaults.url,defaults.params).done(function(response){
					var res = $.parseJSON(response);
					if(res.redirect){
		            	window.location.href = res.redirect;
		            	return false;
		            }
				  	if(typeof defaults.onComplete == 'string'){
				  		eval(defaults.onComplete);
				  	}else if(typeof defaults.onComplete == 'function'){
				  		defaults.onComplete.call(this,response);
				  	}
				});	
	    	}	
		}
	}
	var newObj = new task();
	newObj.init();
	return newObj;
})(jQuery,window);

/*function wpsOpenModalPopup(popup)
{
	jQuery('#'+popup).show();
}*/

function wpsCloseModalPopup(popup)
{
	jQuery('#'+popup).hide();
}

function wpsAddTask()
{
	url = jQuery('#wpsAjaxurl').val();
	jQuery(".wps-loading-image").show();
	jQuery('.wps-form-msg').html("");
	Task.showLoader();
	var formData = jQuery('#frmWpsAddTask').serialize();
	
	var pageURL = jQuery(location).attr("href");
	jQuery.post(url, formData, function(response) {
		
		jQuery(".wps-loading-image").hide();		

		//alert(response);
		var json_array = jQuery.parseJSON(response);
		if(json_array['code'] == 1){
			jQuery('#frmWpsAddTask')[0].reset();
			jQuery('.wps-form-msg').html("<div class='wpsMsg success'>"+json_array['message']+"</div>");
			window.location.reload();
			window.location.href = pageURL;
		}else{
			Task.hideLoader();
			jQuery('.wps-form-msg').html("<div class='wpsMsg warning'>"+json_array['message']+"</div>");
		}
		return false;

	});
	return false;
}

function wspAddDefaultClassOnBody()
{
	jQuery('body').addClass("wsp_default_class");
}
//Select2 item show format
function showItemFormat (option) {
	//console.log(option);
	var originalOption = option.element;
	if (!option.id) { return option.text; }
	var ob = '<img src="'+web_smarter_js_object.baseUrl+'images/priority/'+option.id+'.png" width="25px" style="margin-right:8px;vertical-align: middle;" /> '+ option.text ;	// replace image source with option.img (available in JSON)
	return ob;
};
//Select2 selected item format
function selectedItemFormat(option) {
    if (option.id.length > 0 ) {
        return '<img src="'+web_smarter_js_object.baseUrl+'images/priority/'+option.id+'.png" width="25px" style="margin-right:8px;vertical-align: middle;" /> '+option.text;
    } else {
        return option.text;
    }
}

jQuery(document).ready(function() {

	if(web_smarter_js_object.addClass==1){
		wspAddDefaultClassOnBody();
		jQuery('.select2').select2();
	}
    jQuery('#upload_image_button').click(function() {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });

    window.send_to_editor = function(html) {
        jQuery(".image_wrapper").html('');
        jQuery(".image_wrapper").html(html);
        imgurl = jQuery('.image_wrapper img').attr('src');
        jQuery('#upload_image').val(imgurl);    
        tb_remove();
    }
    var checkChecked = 1;
    jQuery('.wpsSettings #add_feedback_btn_visitors').change(function(){
    	var front_for_admin = false;
    	if(jQuery('.wpsSettings').find('#add_task_btn_on_front_client').is(':checked') && !front_for_admin && checkChecked==1){
    		front_for_admin = true;
    	}
    	checkChecked = checkChecked + 1;
    	
    	if(jQuery(this).is(":checked")){
    		jQuery('.wpsSettings').find('#add_task_btn_on_front_client').prop("checked",true);
    	}else{
    		if(!front_for_admin){
    			jQuery('.wpsSettings').find('#add_task_btn_on_front_client').prop("checked",false);
    		}
    	}
    })
});

jQuery(document).ready(function() {
	jQuery('.wps_page_wrap #post-message').submit(function(){ 
        var html = '<div class="wps-modal-backdrop fade show"></div>'+'<div class="wpsOverlay"><i class="fa fa-spinner fa-spin fa-5x"></i><p>LOADING...</p></div>';
        if(jQuery('.wps_page_wrap #message').val().length>0)
        {
          jQuery('<div class="post_loading">').append(html).appendTo('body');
        }
        return true;
      });
});


jQuery(function($){
	$("tr.wpsNotify td").click(function(){
		if($(this).attr('class')!='website-link'){
			var link = $(this).parent().find('td:nth-child(2)').find('a').attr('href');
			window.location.href = link;
			return false;
		}
	})
	$(".wpsTaskAdminBtn,.wpsTaskPopupBtn").click(function(){
		var link = $(this).attr('data-action');
		Task.showLoader();
      	Task.callAjax({	url:task_ajax_object.url,
					 	params:{action:"task_popup",link:link},
						onComplete:function(response){
							response = $.parseJSON(response);
							Task.hideLoader();
							$('#'+response.id).remove()
							$('html').append(response.html);
							//$('#'+response.id).modal({backdrop: 'static', keyboard: false})  
							$('#'+response.id).show();
							$('.priority').select2({
								templateResult: showItemFormat,
								escapeMarkup: function (m) {
									return m;
								},
								templateSelection: selectedItemFormat
							});
						}
					});
	});
	$(document).on('click','#wps-admin-task-popup .file-take',function(){
		$('#wps-admin-task-popup .browse').trigger('click');
	});
	$(document).on('change','#wps-admin-task-popup .browse',function(){
		$('.wps-form-msg').html("");
		$('#wps-admin-task-popup  .wps_uploadimg_btn').addClass('hide');
		$('#wps-admin-task-popup  #taskUploadImageContainer').removeClass('hide');
		$('#wps-admin-task-popup  .wpsSubmitBtn').addClass('disabled').attr('disabled',true);
		var fd = new FormData();
        var files = $(this)[0].files[0];
        //alert(files);
        var obj = $(this);
        fd.append('file',files);
        fd.append('action','wps_task_upload_image');
        fd.append('file_name',$('#task_file_name').val());
        Task.showLoader({wrapper:'#taskUploadImageContainer'});
      	Task.callAjax({	url:task_ajax_object.url,uploadImg:true,
					 	params:fd,
						onComplete:function(response){
							response = $.parseJSON(response);
							Task.hideLoader();
							//obj.attr('disabled',true);
							//$('#wps-admin-task-popup .capture').attr('disabled',true);
							//$('#wps-admin-task-popup  #taskUploadImageContainer').removeClass('hide');
							if(response.image){
								$('#wps-admin-task-popup #taskUploadImage').attr("src",response.image);
								$('#wps-admin-task-popup #task_file_name').val(response.fileName);
								$('#wps-admin-task-popup  .note').removeClass('hide');
								editImage();
							}else{
				         		$('#wps-admin-task-popup  #taskUploadImageContainer').addClass('hide');
								$('#wps-admin-task-popup  .wps_uploadimg_btn').removeClass('hide');
								$('#wps-admin-task-popup  .wpsSubmitBtn').removeClass('disabled').removeAttr('disabled');
								$('.wps-form-msg').html("<div class='wpsMsg warning'>There is a problem in image capturing.</div>");
				         	}
						}
					});
	})
	var editImage = function (){
		var dkrm = new Darkroom('#wps-admin-task-popup #taskUploadImage', {
		      // Size options
		      minWidth: 100,
		      minHeight: 100,
		      maxWidth: 500,
		      maxHeight: 400,
		      ratio: 5/4,
		      backgroundColor: '#000',

		      // Plugins options
		      plugins: {
		        //save: false,
		        crop: {
		          quickCropKey: 67, //key "c"
		          //minHeight: 50,
		          //minWidth: 50,
		          //ratio: 4/3
		        },
		      	save: {
			      callback: function() {
			      	var oBlob = dkrm.sourceImage.toDataURL();
			      	$('#wps-admin-task-popup #taskUploadImageContainer').attr({"style":"overflow:hidden"});
			      	Task.showLoader({wrapper:'#wps-admin-task-popup #taskUploadImageContainer'});
			      	Task.callAjax({	url:task_ajax_object.url,
								 	params:{
									    	'action' : 'wps_task_upload_image',
									    	'file_name' : $('#wps-admin-task-popup #task_file_name').val(),
									        base64data : oBlob
									},
									onComplete:function(response){
										response = $.parseJSON(response);
										Task.hideLoader();
										$('#wps-admin-task-popup #taskUploadImageContainer').removeAttr("style");
										//$('#wps-admin-task-popup #taskUploadImageContainer').html("<img id='taskUploadImage' src='"+response.image+"'>");
		    							$('#wps-admin-task-popup #task_file_name').val(response.fileName);
		    							$('#wps-admin-task-popup #taskUploadImageContainer').html("<canvas id='editCanvas' width='500' height='400'>");
										//$('#wps-admin-task-popup #taskUploadImageContainer').html("<canvas id='editCanvas' >");

										$('#wps-admin-task-popup .fabric-tool-container').removeClass("hide");
										/*var tmpImg = new Image();
										tmpImg.src = response.image;
										$(tmpImg).one('load',function(){
										  orgWidth = tmpImg.width;
										  orgHeight = tmpImg.height;
										  alert(orgWidth+' test demo '+orgHeight);
										  
										});*/
										var canvas = new fabric.Canvas('editCanvas');
										fabric.Image.fromURL(response.image, function(myImg) {
										 //i create an extra var for to change some image properties
										 var myImg = myImg.set({ left: 0, top: 0 ,width:500,height:400});//,alignX:'mid', alignY:'mid',meetOrSlice:'slice' 
										 //canvas.add(myImg); 
											 canvas.setBackgroundImage(myImg, canvas.renderAll.bind(canvas), {
								               scaleX: canvas.width / myImg.width,
								               scaleY: canvas.height / myImg.height
								            });
										});

										CanvasDrawer.canvasObj = canvas;
										CanvasDrawer.bindEvents();  
										//$('#wps-admin-task-popup .fabric-tool-container .fab-tool[data-val="'+CanvasDrawer.strokeColor+'"]').parent('li').attr('class','active');
			        

									}
								});
			      	//saveImage(oBlob,false);
			        // do what you want
			        this.darkroom.selfDestroy();
			        $('#wps-admin-task-popup #taskUploadImageContainer').find('img').addClass('hide')
			        $('#wps-admin-task-popup #taskUploadImageContainer').find('img').attr({"width":'500px','height':"400px;'"})
			        return true;
			      }
			    }
		      },

		      // Post initialize script
		      initialize: function() {
		        var cropPlugin = this.plugins['crop'];
		        // cropPlugin.selectZone(170, 25, 300, 300);
		        cropPlugin.requireFocus();
		      }
		    });
	}
	function SnapShotDOM(target,call){
	    //var data = target.className;
	    //target.className += " html2canvasreset";//set className - Jquery: $(target).addClass("html2canvasreset");
	    html2canvas(target).then(canvas => {
	            call(canvas);
	        }
	    );
	}
	$(document).on('click','#wps-admin-task-popup .capture',function(){
		if(!$(this).attr('disabled')){
			$('.wps-form-msg').html("");
			//$(this).attr('disabled',true);
			//$('#wps-admin-task-popup .browse').attr('disabled',true);
			$('#wps-admin-task-popup  #taskUploadImageContainer').removeClass('hide');
			$('#wps-admin-task-popup  .wps_uploadimg_btn').addClass('hide');
			$('#wps-admin-task-popup  .wpsSubmitBtn').addClass('disabled').attr('disabled',true);
			Task.showLoader({wrapper:'#wps-admin-task-popup #taskUploadImageContainer'});
			var imageData;
			SnapShotDOM(document.body,function(canvas){
			    imageData = canvas.toDataURL("image/png", 1.0);
				$('#wps-admin-task-popup  .wps_uploadimg_btn').addClass('hide');
				//$('#wps-admin-task-popup #taskUploadImage').attr("src",imageData);
				
				Task.callAjax({	url:task_ajax_object.url,
				 	params:{
					    	'action' : 'wps_task_upload_image',
					    	'file_name' : $('#wps-admin-task-popup #task_file_name').val(),
					        base64data : imageData
					},
					onComplete:function(response){
						response = $.parseJSON(response);
						Task.hideLoader();
						//$('#wps-admin-task-popup  #taskUploadImageContainer').removeClass('hide');
						if(response.image){
							$('#wps-admin-task-popup #taskUploadImage').attr("src",response.image);
							$('#wps-admin-task-popup #task_file_name').val(response.fileName);
							$('#wps-admin-task-popup  .note').removeClass('hide');
				         	editImage();
			         	}else{
			         		$('#wps-admin-task-popup  #taskUploadImageContainer').addClass('hide');
							$('#wps-admin-task-popup  .wps_uploadimg_btn').removeClass('hide');
							$('#wps-admin-task-popup  .wpsSubmitBtn').removeClass('disabled').removeAttr('disabled');
							$('.wps-form-msg').html("<div class='wpsMsg warning'>There is a problem in image capturing.</div>");
			         	}
					}
				});	
			});
			/*html2canvas(document.body).then(canvas => {
				imageData = canvas.toDataURL("image/png", 1.0);
				$('#wps-admin-task-popup  .wps_uploadimg_btn').addClass('hide');
				//$('#wps-admin-task-popup #taskUploadImage').attr("src",imageData);
				
				Task.callAjax({	url:task_ajax_object.url,
				 	params:{
					    	'action' : 'wps_task_upload_image',
					    	'file_name' : $('#wps-admin-task-popup #task_file_name').val(),
					        base64data : imageData
					},
					onComplete:function(response){
						response = $.parseJSON(response);
						Task.hideLoader();
						//$('#wps-admin-task-popup  #taskUploadImageContainer').removeClass('hide');
						if(response.image){
							$('#wps-admin-task-popup #taskUploadImage').attr("src",response.image);
							$('#wps-admin-task-popup #task_file_name').val(response.fileName);
							$('#wps-admin-task-popup  .note').removeClass('hide');
				         	editImage();
			         	}else{
			         		$('#wps-admin-task-popup  #taskUploadImageContainer').addClass('hide');
							$('#wps-admin-task-popup  .wps_uploadimg_btn').removeClass('hide');
							$('#wps-admin-task-popup  .wpsSubmitBtn').removeClass('disabled').removeAttr('disabled');
							$('.wps-form-msg').html("<div class='wpsMsg warning'>There is a problem in image capturing.</div>");
			         	}
					}
				});		
			});*/
		}
	});
	
	$(document).on('click','.fabric-tool-container .fab-tool', function () {
		
		if($(this).attr('data-type')=='color'){
			if($(this).attr('data-val')){
				$(this).parents('ul:first').find('li').removeAttr('class');
				$(this).parent('li').attr('class','active');
				CanvasDrawer.setStroke({'color':$(this).attr('data-val')});
			}
		}else if($(this).attr('data-type')=='size'){
			if($(this).attr('data-val'))
			CanvasDrawer.setFontSize({'size':$(this).attr('data-val')});
		}else{
			CanvasDrawer.drawerType = $(this).attr('data-type');	
		}
	});

	$(document).on('change','.fabric-tool-container  .change-object-attr',function(){
		var val = $(this).find('option:selected').val();
		if(val){
			if($(this).attr('data-type')=='color')
			CanvasDrawer.setStroke({'color':val});
			if($(this).attr('data-type')=='size')
			CanvasDrawer.setFontSize({'size':val});
		}
	});

	$(document).on('click','.fabric-tool-container .fab-tool-save',function(){
		var imageData = $('#taskUploadImageContainer #editCanvas')[0].toDataURL();
		Task.showLoader({wrapper:'#wps-admin-task-popup #taskUploadImageContainer'});
		Task.callAjax({	url:task_ajax_object.url,
			 	params:{
				    	'action' : 'wps_task_upload_image',
				    	'file_name' : $('#wps-admin-task-popup #task_file_name').val(),
				        base64data : imageData
				},
				onComplete:function(response){
					response = $.parseJSON(response);
					Task.hideLoader();
					$('#wps-admin-task-popup  .note').addClass('hide');
					$('#wps-admin-task-popup  .wps_uploadimg_btn').removeClass('hide');
					//$('#wps-admin-task-popup .browse').removeAttr('disabled');
					//$('#wps-admin-task-popup .capture').attr('disabled',false);
					$('#wps-admin-task-popup .fabric-tool-container').addClass('hide');
					$('#wps-admin-task-popup #taskUploadImageContainer').html("<img id='taskUploadImage' src='"+response.image+"'>");
					$('#wps-admin-task-popup #task_file_name').val(response.fileName);
					$('#wps-admin-task-popup  .wpsSubmitBtn').removeClass('disabled').attr('disabled',false);
				}
			});		
	});

	/*Transaction Page Js*/
	if($('#reportrange').length>0){
		var custome = $('#reportrange input').val().split('-');
	    var start = moment();
	    var end = moment();
	    if(custome.length==2){
	    	var start = moment(custome[0]);
		    var end = moment(custome[1]);
	    }

	    function cb(start, end) {
	        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	        $('#reportrange input').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
	    }

	    $('#reportrange').daterangepicker({
	        startDate: start,
	        endDate: end,
	        maxDate:moment(),
	        ranges: {
	           //'Today': [moment(), moment()],
	           //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	           'Last Week': [moment().subtract(1, 'weeks').startOf('week'), moment().subtract(1, 'weeks').endOf('week')],
	           //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'This Month': [moment().startOf('month'), moment()],
	           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	        }
	    }, cb);
	    if(custome.length==2){
	    cb(start, end);
		}

	    $("#trans-go").click(function(){
			var selectDate = $('#reportrange input').val().split(' - ');
			window.location.href = $(this).parents("form:first").attr('action')+"&start="+selectDate[0]+"&end="+selectDate[1];
		})
	}
	$('.wpsSettings .tab a.tablinks').click(function(e){
		e.preventDefault();
		var tab = $(this).attr('href').substr(1, $(this).attr('href').length);
		$('.tab-container').find('div.tab-control').addClass('hide');
		$('#'+tab).removeClass('hide');
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
	});

	//Subscription Custom Plan manage
	$('.subscr_plan').change(function(e){
		if($(this).val()=='custom'){
			$(this).parents('tr').find('button[type="submit"]').addClass('hide');
			$(this).parents('table:first').find('tr#custom-plan').removeClass('hide');
			$(this).parents('table:first').find('tr#custom-plan').find('.custom_class').attr('required',true);
		}else{
			$(this).parents('tr').find('button[type="submit"]').removeAttr('disabled').removeClass('hide disabled');
			$(this).parents('table:first').find('tr#custom-plan').addClass('hide');
			$(this).parents('table:first').find('tr#custom-plan').find('.custom_class').val(null).trigger("change");
			$(this).parents('table:first').find('tr#custom-plan').find('.custom_class').removeAttr('required');
		}
	});
	//Setting page show loadder

	//Setting Form submit when any change occur in form
	$('.wps_setting').on('change keypress',function(e){
		$(this).parents("form:first").find('button[type="submit"],input[type="submit"]').removeClass('disabled').removeAttr('disabled');
	});

	//Input allow number or decimal only when data-type set: number or decimal
	$("input[data-type='number'],input[data-type='decimal']").on("keypress keyup blur",function (event) {
	    $(this).val($(this).val().replace(/[^0-9\.]/g,''));
	    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && event.which!=8 && event.which!=0 && (event.which < 48 || event.which > 57)) {
	        event.preventDefault();
	    }
	    else{
	    	if($(this).attr('data-type')=='decimal'){
		    	$(this).blur(function(){
		    		if($(this).val().length>0){
		    			var value = parseFloat($(this).val()).toFixed(2);
		    			/*var precision = 1;
						var multiplier = Math.pow(10, precision || 0);
						var roundOff = (Math.round(value * multiplier) / multiplier).toFixed(2);*/
		    			$(this).val(value);
		    		}
		    		else{
		    			var value = parseFloat(0).toFixed(2);
		    			$(this).val(value);
		    		}
			   });
	    	}
	    }
	});
});

//this function includes all necessary js files for the application
function include(file){

  var script  = document.createElement('script');
  script.src  = file;
  script.type = 'text/javascript';
  script.defer = true;

  document.getElementsByTagName('body').item(0).appendChild(script);
}

/* include any js files here */
/*include('http://localhost/wordpress/wp-content/plugins/web-smarter/assets/js/html2canvas/html2canvas.js');*/


