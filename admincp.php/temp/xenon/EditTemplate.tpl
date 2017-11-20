<form role="form" class="form-horizontal" method="post" enctype="multipart/form-data">
	<div class="form-group">
        <textarea name="File" class="form-control ckeditor" rows="5">{File}</textarea>
    </div>
    <button type="submit" class="btn ButtonSAVE">
        <i class="fa-check"></i>
        <span>Сохранить</span>
    </button>
</form>
<script>	
$(document).ready(function(){
	tinymce.init({
	  selector: 'textarea',
	  height: 500,
	  language : "ru",
	  plugins: [
	        "autolink link",
	        "searchreplace code fullscreen"
	    ],
	    toolbar: "undo redo | link",
	  content_css: {css},
	   valid_elements : "*[*]",
		image_advtab: true , 
		cleanup : false,
		verify_html : false,
		cleanup_on_startup : false,
		forced_root_block : "",
		validate_children : false,
		remove_redundant_brs : false,
		remove_linebreaks : false,
		force_p_newlines : false,
		force_br_newlines : false,
		valid_children : "+li[p|img|br|strong],+ol[p|img|br|strong],+ul[p|img|br|strong]",
		validate: false,
		fix_table_elements : false,
		fix_list_elements:false,
	});
});
</script>