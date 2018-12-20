<?php global $redux_demo, $email_subject; ?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:400,400i,500,500i,700,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i,900,900i" rel="stylesheet">
    <style>
        body{
            margin: 0;
            padding: 0;
        }
        .primary-color{
            color: rgba(172, 188, 236, 0.35) !important;
        }
        @media only screen and (min-width: 640px) {
            .classiera-email-content{
                width: 600px !important;
                margin: 0 auto !important;
            }
        }
        @media only screen and (max-width: 630px) {
            .classiera-email-content{
                width: 100% !important;
                text-align: center !important;
            }
            .classiera-column-3{
                width: 100% !important;
                margin-bottom: 10px !important;
                text-align: center !important;
            }
            .classiera-column-6{
                width: 100% !important;
                text-align: center !important;
            }
        }
        @media only screen and (min-width: 640px) and (max-width: 768px) {
            .classiera-column-3{
                width: 33% !important;
            }
            .classiera-column-6{
                width: 32% !important;
            }
        }
    </style>
</head>	
<body>
	<div style="width: 100%; background:rgba(155, 155, 155, 0.21);">
		<div class="classiera-email-topbar" style="padding: 5px 30px;">
            <div class="" style="text-align: center">
				<?php $logoImg = get_template_directory_uri() . '/assets/images/Logo_slogen.png'; ?>
                <img src="<?php echo esc_url($logoImg); ?>" style="" alt="PERFIT">
            </div>
        </div>