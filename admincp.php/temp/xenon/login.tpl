<!DOCTYPE html>
<html lang="{langPanel}">
<head>
	<meta charset="{C_charset}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Cardinal Engine Admin Panel" />
	<meta name="author" content="KilleR" />
	
	<title>Admin Panel for {L_sitename}</title>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arimo:400,700,400italic">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/fonts/linecons/css/linecons.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/fonts/fontawesome/css/font-awesome.min.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/bootstrap.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-core.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-forms.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-components.css?14">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/xenon-skins.css?1">
	<link rel="stylesheet" href="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/css/custom.css?{S_time}">
	{css_list}

	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/jquery-1.11.1.min.js?1"></script>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	
</head>
<body class="page-body login-page bgFon">
	<span class="imgHere"></span>
	<div class="login-container">
	
		<div class="row">
		
			<div class="col-sm-12">
			
				<script type="text/javascript">
					jQuery(document).ready(function($)
					{
						// Reveal Login form
						setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);
						
						
						// Validation and Ajax action
						$("form#login").validate({
							rules: {
								username: {
									required: true
								},
								
								passwd: {
									required: true
								}
							},
							
							messages: {
								username: {
									required: '{L_"Пожалуйста введите имя пользователя"}.'
								},
								
								passwd: {
									required: '{L_"Пожалуйста введите пароль"}.'
								}
							},
							
							// Form Processing via AJAX
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
									url: "{C_default_http_host}{D_ADMINCP_DIRECTORY}/?pages=login",
									method: 'POST',
									dataType: 'json',
									data: {
										do_login: true,
										page: 'login',
										method: $(form).find('#method').val(),
										username: $(form).find('#username').val(),
										passwd: $(form).find('#passwd').val(),
									},
									success: function(resp)
									{
										show_loading_bar({
											delay: .5,
											pct: 100,
											finish: function(){
												
												// Redirect after successful login page (when progress bar reaches 100%)
												if(resp.accessGranted)
												{
													window.location.href = '{C_default_http_host}{D_ADMINCP_DIRECTORY}/{ref}';
												}
																							else
												{
													toastr.error(resp.errors, "{L_"Не корректно введены данные!"}", opts);
													$passwd.select();
												}
																						}
										});
										
																		}
								});
								
							}
						});
						
						// Set Form focus
						$("form#login .form-group:has(.form-control):first .form-control").focus();
					});
				</script>
				
				<!-- Errors container -->
				<div class="errors-container">
				
									
				</div>
				
				<!-- Add class "fade-in-effect" for login form effect -->
				<form method="post" role="form" id="login" class="login-form fade-in-effect" autocomplete="off">
					<input type="hidden" name="method" id="method" value="login" />
					<div class="login-header">
						<p>{L_"Уважаемый пользователь, авторизируйтесь в админ-панели!"}</p>
					</div>
	
					
					<div class="form-group">
						<label class="control-label" for="username">{L_"Имя пользователя"}</label>
						<input type="text" class="form-control input-dark" name="username" id="username" autocomplete="off" />
					</div>
					
					<div class="form-group">
						<label class="control-label" for="passwd">{L_"Пароль"}</label>
						<input type="password" class="form-control input-dark" name="passwd" id="passwd" autocomplete="off" value=" " onclick="if(this.value == ' ') this.value=''" />
					</div>
					
					<div class="form-group">
						<button type="submit" class="btn btn-dark  btn-block text-left">
							<i class="fa-lock"></i>{L_"Войти"}
						</button>
					</div>
					
				</form>
				
			</div>
			
		</div>
		
	</div>




	<!-- Bottom Scripts -->
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/bootstrap.min.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/TweenMax.min.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/resizeable.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/joinable.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-api.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-toggles.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/jquery-validate/jquery.validate.min.js?1"></script>
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/toastr/toastr.min.js?1"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="{C_default_http_local}{D_ADMINCP_DIRECTORY}/assets/xenon/js/xenon-custom.js?2"></script>
	{js_list}

</body>
</html>