
<table width="100%" >
    <thead style="height: 100px;background-color: #0A74BA;border-bottom: 0;position: relative;">
        <tr>
            <th colspan="2" style="font-size: 17px;padding-top: 20px;color: #ffffff;text-align: center;font-family: 'Segoe UI Light', 'Helvetica Neue', 'RobotoLight', 'Segoe UI', 'Segoe WP', sans-serif;font-weight: 100;margin-top: 5px;font-size: 43px;">
                Recover password
            </th>
        </tr>
    </thead>
    <!-- end #header -->
    <tbody>
        <tr>
            <td><img src="<?php echo Router::url('/', true) . '/img/markyLogo.png'; ?>" alt="image" title="image" style="padding: 10px;border: 1px solid #e4e4e4;margin-left: 10px;margin-right: 20px;position: relative;top: -71px;"/>
            </td>
            <td style="background: #FFFFFF;min-height: 290px;position: relative;padding: 20px 0;font-family: AdelleWeb-Bold, Georgia, 'Times New Roman', serif;font-size: large;">

                <p style="color: #777777">
                    This mail has been sent from the application Marky, it use for recover your password.
                    If you have not done this operation, please change your email. Even you must use your 
                    new password the next time you enter Marky. Sorry for the inconvenience. Please do not reply to this mail.

                </p>
                <p>
                <p>
                    Your username is: <span style="font-weight: bold;"><?php echo $user ?> </span>
                <p>
                    Your new password is: <span style="font-weight: bold;"><?php echo $password ?></span>
                </p>
                <div class="actionStart">						
                    <?php
                    echo $this->Form->create('User', array('url' => Router::url('/', true) . 'users/login'));
                    echo $this->Form->input('username', array('type' => "hidden", 'value' => $user));
                    echo $this->Form->input('password', array('type' => "hidden", 'value' => $password));
                    ?>
                    <button style="background-color: #eeeeee;border: none;padding: 6px;width: 112px;color: #444;font-family: Lato, Helvetica, Arial, sans-serif;font-size: 12px;text-transform: uppercase;height: 31px;" 
                            type="submit">
                        Go to Marky
                        <img  alt="GoTo" src="<?php echo Router::url('/', true) . '/img/share.svg'; ?>" style="background-color: #08C;padding: 2px;position: relative;left: 71px;top: -21px;width: 24px;"> 
                    </button>
                    <?php echo $this->Form->end(); ?>															
                </div>
            </td>
        </tr>
    </tbody>
    <!-- end #content -->
    <tfoot style="height: 100px;background-color: #efefef;">
        <tr style="height: 80px !important;" >
            <td colspan="2"><a href="http://sing.ei.uvigo.es/marky/" title="Marky" target="_blank" style="color: #909090;padding: 0 15px;text-decoration: none;font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif !important;">Marky About</a>
                <a href="http://sing.ei.uvigo.es" title="sing" target="_blank" style="color: #909090;padding: 0 15px;text-decoration: none;font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif !important;">Sing</a></td>
        </tr>
    </tfoot>
    <!-- end #footer-->
</table>
<!-- end #wrapper-->
