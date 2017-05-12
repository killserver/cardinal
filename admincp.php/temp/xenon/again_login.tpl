<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Xenon Boostrap Admin Panel" />
	<meta name="author" content="" />
	
	<title>Xenon - Homepage</title>

	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Arimo:400,700,400italic">
	<link rel="stylesheet" href="assets/xenon/css/fonts/linecons/css/linecons.css?1">
	<link rel="stylesheet" href="assets/xenon/css/fonts/fontawesome/css/font-awesome.min.css?1">
	<link rel="stylesheet" href="assets/xenon/css/bootstrap.css?1">
	<link rel="stylesheet" href="assets/xenon/css/xenon-core.css?1">
	<link rel="stylesheet" href="assets/xenon/css/xenon-forms.css?1">
	<link rel="stylesheet" href="assets/xenon/css/xenon-components.css?13">
	<link rel="stylesheet" href="assets/xenon/css/xenon-skins.css?1">
	<link rel="stylesheet" href="assets/xenon/css/custom.css?1">

	<script src="assets/xenon/js/jquery-1.11.1.min.js?1"></script>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	
</head>
<body class="page-body lockscreen-page">

	<div class="login-container">
	
		<div class="row">
		
			<div class="col-sm-12">
				
				<script type="text/javascript">
					jQuery(document).ready(function($)
					{
						// Reveal Login form
						setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);
						
						
						// Clicking on thumbnail will focus on password field
						$(".user-thumb a").on('click', function(ev)
						{
							ev.preventDefault();
							$("#passwd").focus();
						});
						
						
						// Form validation and AJAX request
						$(".lockcreen-form").validate({
							rules: {
								passwd: {
									required: true
								}
							},
							
							messages: {
								passwd: {
									required: 'Please enter your password.'
								}
							},
							
							submitHandler: function(form)
							{
								show_loading_bar(70); // Fill progress bar to 70% (just a given value)
								
								var $passwd = $(form).find('#passwd'),
								 	opts = {
										"closeButton": true,
										"debug": false,
										"positionClass": "toast-top-full-width",
										"onclick": null,
										"showDuration": "300",
										"hideDuration": "1000",
										"timeOut": "5000",
										"extendedTimeOut": "1000",
										"showEasing": "swing",
										"hideEasing": "linear",
										"showMethod": "fadeIn",
										"hideMethod": "fadeOut"
									};
								
								$.ajax({
									url: "{C_default_http_host}admincp.php/?pages=login",
									method: 'POST',
									dataType: 'json',
									data: {
										do_login: true,
										page: 'alogin',
										method: $(form).find('#method').val(),
										username: '{U_username}', // user is known in this case
										passwd: $passwd.val(),
									},
									success: function(resp)
									{
										show_loading_bar({
											delay: .5,
											pct: 100,
											finish: function(){
												
												if(resp.accessGranted)
												{
													// Redirect after successful login page (when progress bar reaches 100%)
													window.location.href = '{C_default_http_host}admincp.php/{ref}';
												}
												else
												{
													toastr.error(resp.errors, "Invalid Login!", opts);
													$passwd.select();
												}
											}
										});
									}
								});
							}
						});
						
						// Set Form focus
						$("form#lockscreen .form-group:has(.form-control):first .form-control").focus();
					});
				</script>
				
				<form role="form" id="lockscreen" class="lockcreen-form fade-in-effect" autocomplete="off">
					<input type="hidden" name="method" value="login" />
					<div class="user-thumb">
						<a href="#">
							<img src="http://img2.wikia.nocookie.net/__cb20130512094126/sword-art-online/pl/images/thumb/a/a4/Akihiko_Kayaba.png/500px-Akihiko_Kayaba.png" class="img-responsive img-circle" />
						</a>
					</div>
					
					<div class="form-group">
						<h3>Welcome back, {U_username}!</h3>
						<p>Enter your password to access the admin.</p>
						
						<div class="input-group">
							<input type="password" class="form-control input-dark" name="passwd" id="passwd" placeholder="Password" autocomplete="off" />
							<span class="input-group-btn">
								<button type="submit" class="btn btn-primary">Log In</button>
							</span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">
			jQuery(document).ready(function($) {
				jQuery('body').attr('class', jQuery('body').attr('class').replace(/skin-[a-z]+/i, '')).addClass(Cookies.get('current-skin') ? (' skin-'+Cookies.get('current-skin')) : '');
				console.log(Cookies.get('current-skin'));
			});
	</script>
	<!-- Bottom Scripts -->
	<script src="assets/xenon/js/bootstrap.min.js?1"></script>
	<script src="assets/xenon/js/TweenMax.min.js?1"></script>
	<script src="assets/xenon/js/resizeable.js?1"></script>
	<script src="assets/xenon/js/joinable.js?1"></script>
	<script src="assets/xenon/js/xenon-api.js?1"></script>
	<script src="assets/xenon/js/xenon-toggles.js?1"></script>
	<script src="assets/xenon/js/jquery-validate/jquery.validate.min.js?1"></script>
	<script src="assets/xenon/js/toastr/toastr.min.js?1"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="assets/xenon/js/xenon-custom.js?1"></script>

</body>
</html>