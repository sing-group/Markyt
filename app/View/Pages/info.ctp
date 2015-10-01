<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<h2>if you want to display this information removes the file /Marky/app/View/Pages/info</h2>
<?php
if (version_compare(PHP_VERSION, '5.2.8', '>=')):
    echo '<span class="notice success">';
    echo __d('cake_dev', 'Your version of PHP is 5.2.8 or higher.');
    echo '</span>';
else:
    echo '<span class="notice">';
    echo __d('cake_dev', 'Your version of PHP is too low. You need PHP 5.2.8 or higher to use CakePHP.');
    echo '</span>';
endif;
?>
</p>
<p>
    <?php
    if (is_writable(TMP)):
        echo '<span class="notice success">';
        echo __d('cake_dev', 'Your tmp directory is writable.');
        echo '</span>';
    else:
        echo '<span class="notice">';
        echo __d('cake_dev', 'Your tmp directory is NOT writable.');
        echo '</span>';
    endif;
    ?>
</p>
<p>
    <?php
    $settings = Cache::settings();
    if (!empty($settings)):
        echo '<span class="notice success">';
        echo __d('cake_dev', 'The %s is being used for core caching. To change the config edit APP/Config/core.php ', '<em>' . $settings['engine'] . 'Engine</em>');
        echo '</span>';
    else:
        echo '<span class="notice">';
        echo __d('cake_dev', 'Your cache is NOT working. Please check the settings in APP/Config/core.php');
        echo '</span>';
    endif;
    ?>
</p>
<p>
    <?php
    $filePresent = null;
    if (file_exists(APP . 'Config' . DS . 'database.php')):
        echo '<span class="notice success">';
        echo __d('cake_dev', 'Your database configuration file is present.');
        $filePresent = true;
        echo '</span>';
    else:
        echo '<span class="notice">';
        echo __d('cake_dev', 'Your database configuration file is NOT present.');
        echo '<br/>';
        echo __d('cake_dev', 'Rename APP/Config/database.php.default to APP/Config/database.php');
        echo '</span>';
    endif;
    ?>
</p>
<?php
if (isset($filePresent)):
    App::uses('ConnectionManager', 'Model');
    try {
        $connected = ConnectionManager::getDataSource('default');
    } catch (Exception $connectionError) {
        $connected = false;
        $errorMsg = $connectionError->getMessage();
        if (method_exists($connectionError, 'getAttributes')) {
            $attributes = $connectionError->getAttributes();
            if (isset($errorMsg['message'])) {
                $errorMsg .= '<br />' . $attributes['message'];
            }
        }
    }
    ?>
    <p>
        <?php
        if ($connected && $connected->isConnected()):
            echo '<span class="notice success">';
            echo __d('cake_dev', 'Cake is able to connect to the database.');
            echo '</span>';
        else:
            echo '<span class="notice">';
            echo __d('cake_dev', 'Cake is NOT able to connect to the database.');
            echo '<br /><br />';
            echo $errorMsg;
            echo '</span>';
        endif;
        ?>
    </p>
<?php endif; ?>
<?php
App::uses('Validation', 'Utility');
if (!Validation::alphaNumeric('cakephp')) {
    echo '<p><span class="notice">';
    echo __d('cake_dev', 'PCRE has not been compiled with Unicode support.');
    echo '<br/>';
    echo __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring');
    echo '</span></p>';
}
?>

