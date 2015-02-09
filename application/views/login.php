<!DOCTYPE html> 
<html lang="es-ES">
    <head>
        <title>:: Admin Ventas ::</title>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
        <link rel="stylesheet"    href="<?php echo base_url(); ?>assets/css/style.default.css" type="text/css" />
        <link rel="stylesheet"    href="<?php echo base_url(); ?>assets/css/jquery-impromptu.css" type="text/css" />
        
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-migrate-1.1.1.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui-1.9.2.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/modernizr.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-impromptu.js"></script>
        <!--Archivos del sistema -->
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/system/global_system.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/system/login.js"></script>

        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/custom.js"></script>
        <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>assets/js/excanvas.min.js"></script><![endif]-->
        
    </head>
<body class="loginpage">
    <div class="loginpanel">
        <div class="loginpanelinner">
            <div class="logo animate0 bounceIn">
                <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="" />
            </div>
            <?php 
                $attributes = array('id' => 'login');
                echo form_open('', $attributes);
                echo '<div class="inputwrapper login-alert">';
                echo '<div class="alert alert-error">Invalid username or password</div>';
                echo '</div>';
                echo '<div class="inputwrapper animate1 bounceIn">';
                $attr = array(
                            'name'    => 'id_user',
                            'id'      => 'id_user',
                            'type'    => 'hidden'
                        );  
                echo form_input($attr);
                $attr = array(
                            'name'    => 'user',
                            'id'      => 'user'
                        );  
                echo form_input($attr, '', 'placeholder="Usuario"');
                echo '</div>';
                echo '<div class="inputwrapper animate2 bounceIn">';
                $attr = array(
                            'name'    => 'pwd',
                            'id'      => 'pwd'
                        ); 
                echo form_password($attr, '', 'placeholder="Password"');
                echo '</div>';
                if(isset($message_error) && $message_error){
                echo '<div class="alert alert-error">';
                echo '<button data-dismiss="alert" class="close" type="button">×</button>';
                echo '<strong>Upss!</strong><br>Usuario y/o password incorrectos<br>Intentalo nuevamente.';
                echo '</div>';
                }

                $attr = array(
                            'name'    => 'button',
                            'id'      => 'button_login',
                            'value'   => 'true',
                            'content' => 'Entrar'
                        );
                echo '<div class="inputwrapper animate3 bounceIn">';
                echo form_button($attr);
                echo '</div>';
                echo form_close();
            ?>
        </div>      
    </div>
</body>
</html>    