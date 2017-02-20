var allowed_files = ['xls','xlsx','xlsm','xlst','csv'];
var file_content = null;

function update_sheet_columns(){
    sheetIndex = ($('#excel_sheet').val() != null)?$('#excel_sheet').val():0;
    sheetColumns = '';
    if(file_content != null && file_content[sheetIndex] != null && file_content[sheetIndex]['data'][0] != null){
        file_content[sheetIndex]['data'][0].forEach(function(cell,cellIndex){
            sheetColumns += `<option value="${cellIndex}">${escapeHTML(cell)}</option>`;
        });
    }
    $('.sheet_column').each(function(){
       $(this).html(sheetColumns);
       //console.log(this,sheetColumns); 
    });
}

function upload_file(){
    var files = document.getElementById('excel_file').files;
    $('#upload_result').fadeOut('fast');
   if(files.length > 0){
        var file = files[0];
        var ext = file.name.split('.').pop();
        //Check for file extension
        if(allowed_files.indexOf(ext.toLowerCase()) > -1){
            var formData = new FormData();
            //Show loading div
            with(t = $('link[rel=stylesheet][href*="css/main"]').attr('href'))assets_base=t.substr(0,t.indexOf('css/main')); 
            $('#file_upload').prepend('<div class="overlay form-loading"><img src="'+assets_base+'/images/loading.gif"</div>');
            //Retrieve CSRF token to append it in POST body
            csrf_token = document.querySelector('meta[name=csrf_token]').getAttribute('content');
            //Prepare POST body data
            formData.append('excel_file',file,file.name);
            formData.append('csrf_token',csrf_token);
            formData.append('action','file_upload');
            //Send Request
            $.ajax({
                url:'',
                type:'POST',
                data:formData,
                processData:false,
                contentType:false,
                cache:false,
                success:function(response,status,xhr){
                    try{
                        var result = (typeof(response)=='string')?JSON.parse(xhr.responseText):response;
                    }catch(error){
                        var result = false;
                    } 
                    if(result && result.status == 'success'){
                        $('#upload_result').html(get_alert(result.message,'success')).fadeIn('slow');
                        file_content = result.data;
                        $('#upload_file a.goto-next').click();
                        $('#sheet_config .overlay').fadeOut('slow');
                        $('#db_config .overlay').fadeOut('slow');
                        if(result.data != null){
                            result.data.forEach(function(sheet,sheetIndex){
                                $('#excel_sheet').append(`<option value="${sheetIndex}">${escapeHTML(sheet.sheet_name)}</option>`);
                            });
                            update_sheet_columns();
                        }
                        return;
                    }else{
                        var msg = (result && result.message)?result.message:'فشل رفع الملف برجاء المحاولة مرّة أخرى'; 
                        $('#upload_result').html(get_alert(msg,'error')).fadeIn('slow');
                    }
                },
                error:function(){
                    $('#upload_result').html(get_alert('فشل رفع الملف برجاء المحاولة مرّة أخرى!','error')).fadeIn('slow');
                },
                complete:function(){
                    $('#file_upload').find('.overlay.form-loading').fadeOut('slow');
                }
            })

           
        }else{
            $('#upload_result').html(get_alert('الملف الذي تحاول رفعه غير مسموح به... برجاء رفع ملفات Excel فقط.','error')).fadeIn('slow');
        }
         
   }
   }
function save_data_callback(response,xhr){
    try{
        result = (typeof(response)=='string')?JSON.parse(response):response;
    }catch(e){
        result = false;
    }
    if(result && result.status=='success'){
        var msg = result.message;
        $('#finish').fadeIn('slow');
        $('#db_config a.goto-next').fadeIn('fast').click();
    }else{
        console.log(result);
        var msg = (result && result.message)?result.message:'فشل حفظ البيانات برجاء المحاولة مرّة أخرى';
        $('#db_config .alert').remove();
        $('#db_config .content').prepend(get_alert(msg));
        $('#sheet_config a.goto-next').click();
    }
} 
$('body').on('click','#excel_file',function(){this.value = null}); //reset file choice
$('body').on('change','#excel_file',upload_file);
$('body').on('change','#excel_sheet',update_sheet_columns);
