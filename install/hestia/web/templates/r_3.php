<!-- /** 
*
* Hestia Web Interface
*
* Copyright (C) 2020 Carter Roeser <carter@cdgtech.one>
* https://cdgco.github.io/HestiaWebInterface
*
* Hestia Web Interface is free software: you can redistribute it and/or modify
* it under the terms of version 3 of the GNU General Public License as published 
* by the Free Software Foundation.
*
* Hestia Web Interface is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with Hestia Web Interface.  If not, see
* <https://github.com/cdgco/HestiaWebInterface/blob/master/LICENSE>.
*
*/ -->
<!DOCTYPE html>  
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" sizes="16x16" href="https://cdn.jsdelivr.net/gh/cdgco/HestiaWebInterface@0.1.0-Beta/plugins/images/favicon.png">
        <title>Reset Password</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/gh/cdgco/HestiaWebInterface@0.1.0-Beta/css/style.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/gh/cdgco/HestiaWebInterface@0.1.0-Beta/css/colors/default.css" id="theme"  rel="stylesheet">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Preloader -->
        <div class="preloader">
            <div class="cssload-speeding-wheel"></div>
        </div>
        <section id="wrapper" class="login-register">
            <div class="login-box">
                <div class="white-box">
                    <form class="form-horizontal form-material" autocomplete="off" id="loginform" method="post">
                        <h3 class="box-title m-b-20">Confirm Password</h3>
                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input type="hidden" name="action" value="confirm">
                                <input type="hidden" name="user" value="<?php print_r($_GET['user']); ?>">
                                <input type="hidden" name="user" value="<?php print_r($_GET['code']); ?>">
                                <input class="form-control" type="password" name="password" required="" placeholder="New Password">
                                <input class="form-control" type="password" name="password_confirm" required="" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/cdgco/HestiaWebInterface@0.1.0-Beta/js/main.js"></script>
        <script>
            Waves.attach('.button', ['waves-effect']);
            Waves.init();
        </script>
    </body>
</html>
