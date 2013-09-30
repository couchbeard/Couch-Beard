<?php
/*
  Plugin Name: Couch Beard APIs
  Plugin URI:
  Description: Manage API keys for applications
  Author: Mads Lundt
  Version: 1.0
  Author URI:
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : madslundt@live.dk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $wpdb;
global $table_name;
$table_name = $wpdb->prefix . 'apis';

require_once(__DIR__ . '/couchbeard.php');
require_once(__DIR__ . '/sabnzbd.php');
require_once(__DIR__ . '/couchpotato.php');
require_once(__DIR__ . '/sickbeard.php');
require_once(__DIR__ . '/xbmc.php');
require_once(__DIR__ . '/imdbAPI.php');


function loadStyle()
{
    wp_register_style('style', plugin_dir_url(__FILE__) . 'couch-beard-api.css');
    wp_enqueue_style('style');
}

add_action('admin_enqueue_scripts', 'loadStyle');

function couchbeardapi_activate()
{
    global $wpdb;
    global $table_name;

    $apis = array("Couchpotato", "Sickbeard", "SabNZBD");
    $logins = array("XBMC");
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name)
    {
        $sql = "CREATE TABLE " . $table_name . "(
              ID INT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NOT NULL UNIQUE ,
              api VARCHAR(100) NULL ,
              ip VARCHAR(100) NULL ,
              username VARCHAR(45) NULL ,
              password VARCHAR(45) NULL ,
              login TINYINT(1) DEFAULT 0 NOT NULL ,
              PRIMARY KEY (ID) )
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('api_database_version', '1.0');
    }

    foreach ($apis as $a)
    {
        $wpdb->insert($table_name, array('name' => $a));
    }

    foreach ($logins as $l)
    {
        $wpdb->insert($table_name, array('name' => $l, 'login' => 1));
    }
}

register_activation_hook(__FILE__, 'couchbeardapi_activate');

function couchbeardapi_deactivate()
{
    global $wpdb;
    global $table_name;

    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name)
    {
        $sql = "DROP TABLE " . $table_name;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('api_database_version', '1.0');
    }
}

register_uninstall_hook(__FILE__, 'couchbeardapi_deactivate');

function couchbeardapi_admin_actions()
{
    $page_title = "Couch Beard APIs";
    $menu_title = "Couch Beard";
    //$capability = "manage_options";

    add_menu_page($page_title, $menu_title, 'administrator', 'couchbeardapi_settings', 'couchbeardapi_admin');

    //add_options_page($page_title, $menu_title, $capability, __FILE__, 'couchbeardapi_admin');
}

add_action('admin_menu', 'couchbeardapi_admin_actions');

function couchbeardapi_admin()
{
    global $wpdb;
    global $table_name;
    $list = $wpdb->get_results(
            "
		SELECT *
		FROM $table_name
		"
    );

    if (isset($_POST['apisave']))
    {
        foreach ($list as $a)
        {

            // API
            if ($a->login == 0)
            {
                if (strlen($_POST[$a->name . 'api']) > 2 && strlen($_POST[$a->name . 'ip']) > 2)
                {
                    $wpdb->query($wpdb->prepare(
                                    "
								UPDATE $table_name
								SET api = %s, ip = %s
								WHERE name = %s
							", array(
                                $_POST[$a->name . 'api'],
                                $_POST[$a->name . 'ip'],
                                $a->name
                                    )
                    ));
                }
                else
                {
                    $_POST[$a->name . 'api'] = "";
                    $_POST[$a->name . 'ip'] = "";
                    printf(__('Error in %s', 'wpbootstrap'), $a->name);
                    echo '<br />';
                }
            }
            else
            {
                if (strlen($_POST[$a->name . 'user']) > 2 && strlen($_POST[$a->name . 'pw']) > 2 && strlen($_POST[$a->name . 'ip']) > 2)
                {
                    $wpdb->query($wpdb->prepare(
                                    "
								UPDATE $table_name
								SET ip = %s, username = %s, password = %s
								WHERE name = %s
							", array(
                                $_POST[$a->name . 'ip'],
                                $_POST[$a->name . 'user'],
                                $_POST[$a->name . 'pw'],
                                $a->name
                                    )
                    ));
                }
                else
                {
                    $_POST[$a->name . 'user'] = "";
                    $_POST[$a->name . 'pw'] = "";
                    $_POST[$a->name . 'ip'] = "";
                    printf(__('Error in %s', 'wpbootstrap'), $a->name);
                    echo '<br />';
                }
            }
        }
    }
    ?>
    <div class="wrap">
        <h2>Manage Couch Beard APIs</h2>
    </div>
    <form action="" method="POST" id="content">
        <table class="widefat" id="table">
            <thead>
                <tr>
                    <th> Application </th>
                    <th> Key </th>
                    <th> IP:Port </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th> Application </th>
                    <th> Key </th>
                    <th> IP:Port </th>
                </tr>
            </tfoot>
            <tbody>
                    <?php
                    foreach ($list as $a):
                    ?>
                    <tr>
                        <td id="name"> <strong> <?php echo $a->name; ?> </strong> </td>
                        <?php
                        if ($a->login == 0):
                            ?>
                                <td> <input type="text" name="<?php echo $a->name; ?>api" value="<?php echo (isset($a->api) ? $a->api : $_POST[$a->name . 'api']); ?>" placeholder="API key"> </td>
                                <td> <input type="text" name="<?php echo $a->name; ?>ip" value="<?php echo (isset($a->ip) ? $a->ip : $_POST[$a->name . 'ip']); ?>" placeholder="IP:Port"> </td>
                            <?php
                        else:
                            ?>
                                <td> 
                                    <input type="text" name="<?php echo $a->name; ?>user" value="<?php echo (isset($a->username) ? $a->username : $_POST[$a->name . 'user']); ?>" placeholder="Username"> 
                                    <input type="password" name="<?php echo $a->name; ?>pw" value="<?php echo (isset($a->password) ? $a->password : $_POST[$a->name . 'pw']); ?>" placeholder="Password"> 
                                </td>
                                <td> <input type="text" name="<?php echo $a->name; ?>ip" value="<?php echo (isset($a->ip) ? $a->ip : $_POST[$a->name . 'ip']); ?>" placeholder="IP:Port"> </td>					
                            <?php
                        endif;
                        ?>
                                    </tr>
                        <?php
                    endforeach;
                    ?>	
            </tbody>
        </table>
        <input type="submit" name="apisave" id="sub" value="Save changes" class="button-primary" />
    </form>

<?php
}
?>