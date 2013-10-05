<?php
/*
  Plugin Name: Couch Beard APIs
  Plugin URI:
  Description: Manage API keys and login for applications
  Author: Mads Lundt
  Version: 2.0
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

/*require_once(__DIR__ . '/couchbeard.php');
require_once(__DIR__ . '/sabnzbd.php');
require_once(__DIR__ . '/couchpotato.php');
require_once(__DIR__ . '/sickbeard.php');
require_once(__DIR__ . '/xbmc.php');
require_once(__DIR__ . '/imdbAPI.php');*/


function loadStyle()
{
    wp_register_style('style', plugin_dir_url(__FILE__) . 'couch-beard-api.css');
    wp_enqueue_style('style');
}

add_action('admin_enqueue_scripts', 'loadStyle');



class CouchBeardApi {

    const DOMAIN = 'couchbeard';

    public static $table_name;

    public static $apis = array("Couchpotato", "Sickbeard", "SabNZBD");
    public static $logins = array("XBMC");

    public function __construct() {
        global $status, $page;

        $this->load_dependencies();

        add_action('admin_menu', array(&$this,'add_menu_items'));
    }

    public function add_menu_items() {
        global $submenu;
        add_menu_page(
            'Couchbeard',
            'CouchBeard',
            'activate_plugins',
            'CouchBeard',
            array(&$this,'render_couchbeard_page')
        );
    }

    public function add_couchbeard_settings($settings) {
        $new_settings = array(
            array(
                /*Sections*/
                'name'      => 'CouchBeard',
                'title'     => __('Applications',self::DOMAIN),
                'fields'    => array()
            )
        );
        return array_merge($settings,$new_settings);
    }

    public function render_couchbeard_page() {
?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
<?php
                $this->render_list_table(new Couchbeard_List_Table());
?>
        </div>
<?php
    }

    private function render_list_table($table) {
        $table->prepare_items();   
?>
    <h2><?php $table->get_title(); ?></h2>
    <form id="applicationedit" method="POST">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $table->views(); ?>
        <?php $table->display(); ?>
        <input type="submit" class="button-primary sub" name="submitbutton">
    </form>
<?php
        return $table;
    }


    private function load_dependencies() {
        global $wpdb;

        if (!class_exists('WP_List_Table'))
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

        self::$table_name = $wpdb->prefix . 'apis';

        if ($wpdb->get_var('SHOW TABLES LIKE ' . $this->table_name) != $this->table_name)
        {
            $sql = "CREATE TABLE " . $this->table_name . "(
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

        foreach (self::$apis as $a)
        {
            $wpdb->insert($this->table_name, array('name' => $a));
        }

        foreach (self::$logins as $l)
        {
            $wpdb->insert($this->table_name, array('name' => $l, 'login' => 1));
        }

        require_once(__DIR__ . '/couchbeard-list-table.php');


        if (!class_exists('couchbeard'))
            require_once(__DIR__ . '/couchbeard.php');

        if (!class_exists('sabnzbd'))
            require_once(__DIR__ . '/sabnzbd.php');

        if (!class_exists('couchpotato'))
            require_once(__DIR__ . '/couchpotato.php');

        if (!class_exists('sickbeard'))
            require_once(__DIR__ . '/sickbeard.php');

        if (!class_exists('xbmc'))
            require_once(__DIR__ . '/xbmc.php');

        if (!class_exists('imdbAPI'))
            require_once(__DIR__ . '/imdbAPI.php');
    }

} //class

new CouchBeardApi();

if (isset($_POST['submitbutton'])) {

    // All apis
    foreach (CouchBeardApi::$apis as $app) {
        if (strlen($_POST['user' . $app]) > 2 && strlen($_POST['ip' . $app]) > 2) {
            $wpdb->query($wpdb->prepare(
                "
                UPDATE " . CouchBeardApi::$table_name . "
                SET api = %s, ip = %s
                WHERE name = %s
                ", 
                array(
                    $_POST['api' . $app],
                    $_POST['ip' . $app],
                    $app
                )
            ));
        }
    }

    // All logins
    foreach (CouchBeardApi::$logins as $app) {
        if (strlen($_POST['user' . $app]) > 2 && strlen($_POST['ip' . $app]) > 2) {
            $wpdb->query($wpdb->prepare(
                "
                UPDATE " . CouchBeardApi::$table_name . "
                SET ip = %s, username = %s, password = %s
                WHERE name = %s
                ", 
                array(
                    $_POST['ip' . $app],
                    $_POST['user' . $app],
                    $_POST['pass' . $app],
                    $app
                )
            ));
        }
    }
}
?>