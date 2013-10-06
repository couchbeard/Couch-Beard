<?php
class Couchbeard_List_Table extends WP_List_Table {
	const NAME_SINGULAR = 'Application';
    const NAME_PLURAL = 'Applications';

    protected $title;
    
    public function __construct() {
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => self::NAME_SINGULAR,
            'plural'    => self::NAME_PLURAL,
            'ajax'      => false        //does this table support ajax?
        ) );

        $this->title = "CouchBeard APIs";
    }

    public function get_title() {
        echo $this->title;
    }

    protected function column_default($item, $column_name) {
        switch($column_name) {
        	case 'ID':
        		return $item->ID;
        	case 'application':
        		return $item->name;
        	case 'key':
        		if (!empty($item->login))
        			return '<input type="text" name="user' . $item->ID . '" value="' . $item->username . '" placeholder="' . __('Username', 'couchbeard') . '"> <input type="password" name="pass' . $item->ID . '" value="' . $item->password . '" placeholder="' . __('Password', 'couchbeard') . '">';
        		else
        			return '<input type="text" name="api' . $item->ID . '" value="' . $item->api . '" placeholder="' . __('API', 'couchbeard') . '">';
        	case 'ip':
        		return '<input type="text" name="ip' . $item->ID . '" value="' . $item->ip . '" placeholder="' . __('IP:Port', 'couchbeard') . '">';
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    protected function column_title($item) {
        
        //Build row actions
        $actions = array(
            'clear'      => '<a href="'.add_query_arg(array('page' => $_REQUEST['page'], 'action' => 'clear', $this->_args['singular'] => $item->ID), 'admin.php').'">'.__('Clear').'</a>',
        );
        
        //Return the title contents
        return sprintf('<strong><a href="%1$s">%2$s</a></strong>%3$s',
            add_query_arg(array('page' => $_REQUEST['page'], 'subpage' => 'wpdkatag-objects', $this->_args['singular'] => $item->ID), 'admin.php'),
            $item->ID,
            $this->row_actions($actions)
        );
    }

    public function get_columns() {
        $columns = array(
        	'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'application'     => __('Application'),
            'key'    => __('Key'),
            'ip'	=> __('IP:Port')
        );
        return $columns;
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'], 
            /*$2%s*/ $item->ID
        );
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'application'     => array('application',false)     //true means it's already sorted
        );
        return $sortable_columns;
    }

    public function get_bulk_actions() {
        $actions = array(
            'clear' => __('Clear')
        );
        return $actions;
    }

    protected function process_bulk_action() {
        global $wpdb;
        //Clear when a bulk action is being triggered...
        if($this->current_action() == 'clear') {
            foreach ($_POST[$this->_args['singular']] as $id) {
                $wpdb->query($wpdb->prepare(
                    "
                    UPDATE " . CouchBeardApi::$table_name . "
                    SET api = '', ip = '', username = '', password = ''
                    WHERE ID = %s
                    ", 
                    array(
                        $id
                    )
                ));
            }
            wp_die('Items cleared (or they would be if we had items to clear)!');
        }
    }

    public function prepare_items() {
    	global $wpdb;

        $per_page = $this->get_items_per_page( 'edit_couchbeard_per_page');

        $hidden = array();
        $this->_column_headers = array($this->get_columns(), $hidden, $this->get_sortable_columns());

        $this->process_bulk_action();
                     
        $apps = array();

        $apps = $wpdb->get_results(
            "
			SELECT *
			FROM " . CouchBeardApi::$table_name
	    );

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($apps);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $apps = array_slice($apps,(($this->get_pagenum()-1)*$per_page),$per_page);
        
        // foreach($apps as $tag_k => $tag_v) {
        //     $this->items[] = array("count")
        // }
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $apps;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
}
?>