<div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <!--
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image: url('<?php echo base_url();?>assets/img/default/login-bg.jpg')">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>
                    -->
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto mx-auto">
                        <div class="authentication-form mx-auto">
                            <div class="row">
                                <div class="col-12" >
                                    <img class="mx-auto d-block" style="height: 150px; width: 150px;" src="<?php echo base_url();?>assets/img/default/uisi-logo.png" alt="">
                                </div>
                            </div>
                            <h3 class="text-center">Sistem FRS UISI</h3>
                            <p></p>
                            <div id="alert_notice">
                            </div>
                            
                            <form id="formLogin">
                                <!--
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Username" required="" value="">
                                    <i class="ik ik-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Password" required="" value="">
                                    <i class="ik ik-lock"></i>
                                </div>
                                <div class="row">
                                    <div class="col text-left">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                            <span class="custom-control-label">&nbsp;Remember Me</span>
                                        </label>
                                    </div>
                                    <div class="col text-right">
                                        <a href="forgot-password.html">Forgot Password ?</a>
                                    </div>
                                </div>
                                -->
                                <div class="form-group">
                                    <select class="form-control" name="group_id" id="loginas_select">
                                        <option value="">Login as</option>
                                        <option value="0">Admin</option>
                                        <option value="1">Dosen</option>
                                        <option value="2">Mahasiswa</option>
                                    </select>
                                    <i class="ik ik-shield"></i>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="user_id" id="user_select" style="display: none;">
                                        <option value="">Choose user</option>
                                    </select>
                                    <i class="ik ik-user" style="display: none;"></i>
                                </div>
                                <div class="sign-btn text-center" >
                                    <button class="btn btn-theme" id="loginBtn">Sign In</button>
                                </div>
                            </form>
                            <!--
                            <div class="register">
                                <p>Don't have an account? <a href="register.html">Create an account</a></p>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>