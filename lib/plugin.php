<?php
/**
 *   cache class.
 */

namespace MM;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'MM\Plugin' ) ) {
        
    /**
	 * MM Cache
    */

	class Plugin extends \Memcache{
		

        protected static $instance;

                //needed for path manipulation
        public $plugin_dir;
        public $plugin_path;
        public $plugin_url;
        public $plugin_name;
        
        protected $memcached_servers = array();
        
                
        /**
		 * Static Singleton Factory Method
		 * @return App
		 */
		public static function instance()
        {
			if ( ! isset( self::$instance ) ) {
				$className      = __CLASS__;
				self::$instance = new $className;
			}

			return self::$instance;
		}
        
        /**
		 * Initializes plugin variables and sets up WordPress hooks/actions.
		 */
		protected function __construct()
        {
          
          $this->plugin_path = trailingslashit( dirname( dirname( __FILE__ ) ) );
            $this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
            $this->plugin_url  = plugins_url( $this->plugin_dir );
            
            /* memcached servers are setup in config/keys.php
            *  and established as a global array
            *  in wp-config.php, q.v.
            */

            global $memcached_servers;
            $this->memcached_servers = $memcached_servers;
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		}

        /* void, no args
         * just returns the file path to this plugin
         *
         * @return void
         */
        public function get_plugin_path()
        {
            return $this->plugin_path;
        }

        public function get_plugin_url()
        {
            return $this->plugin_url;
        }

         /*
         * void, no args
         *
         * creates  admin menu and submenu pages
         *
         */
        public function add_admin_menu()
        {

            add_menu_page('Memcached', 'Memcached', 'administrator', 'memcached', array($this, 'memcached'), '', 40);

        }

       
        /* Memcached management Helium submenu page
         *
         */
        public function memcached()
        {
            $this->manager();
        }
        
        /* void, no args
         * main Memcached management handler function
         *
         * prints stats and responds to form submissions (e.g., purge)
         *
         */
        public function manager()
        {

            global $_REAL_POST;

            if($_REAL_POST['purge']) {
                $ip = array_keys($_REAL_POST['purge']);
                $purge = $this->purge($ip[0]);
                \debug($purge);
            }
            
            if($_REAL_POST['view']) {
                $host = array_keys($_REAL_POST['view']);
                
                $view = new \stdClass();
                $view->server = $host[0];
                
                $view->data = $this->get_data(500, $host[0]);
            }

            $servers = array();
            
            foreach($this->memcached_servers as $memcached_server) {
                
                $server = new \stdClass();
                $server->host = $memcached_server;
                $server->port = '11211';
                $server->status = $this->check_status($server->host, $server->port) ? "Online": "Offline";
                
                if($server->status == "Online") {
                
                    $mem = new \Memcache();
                    $mem->addServer($memcached_server, '11211');
                    $stats = $mem->getStats();
                } else {
                    
                    $stats = NULL;
                }
                
                //the overview stats we are interested in
                $hit_rate           = @((real)$stats["get_hits"] / (real)$stats["cmd_get"] *100);
                $hit_rate           = number_format( round($hit_rate,3) );
                $miss_rate          = number_format( 100 - $hit_rate );
                
                $server->hits       = $stats['get_hits'];
                $server->hitrate    = $hit_rate;
                $server->missrate   = $miss_rate;
                $server->misses     = $stats['get_misses'];
                
                $server->connections= $stats['total_connections'];
                $server->objects    = $stats['total_items'];
                $uptime             = (int)$stats['uptime'];
                $server->uptime     = number_format($uptime/3600, 2) . ' hours';
                              
                $server->size       = (real) $stats["limit_maxbytes"]/(1024*1024) . " MB";
                
                $server->gets       = $stats['cmd_get'];
                $server->sets       = $stats['cmd_set'];
                $server->evictions  = $stats['evictions'];
                    
                $servers[] = $server;
                
            }
            
            //include html needed to show the page
            include Plugin::instance()->get_plugin_path() . '/includes/cache-manager.php';

        }
        
        /* Purge function
         *
         * sets memcached server key/values as invalid
         *
         * @param string $host IP or hostname of memcached server
         * @param string $port Optional. Defaults to standard port
         *
         * @since vora
         *
         * @return boolean
         */
        private function purge($host, $port = '11211')
        {

            $mem = new \Memcache();
            $mem->connect($host, $port);
            $purge = $mem->flush();
            
            sleep(1);
            
            return $purge;
            
        }

        
        private function check_status($host, $port) {
            if ( @$this->connect( $host, $port )) {
                return $this->getServerStatus( $host, $port );
            } else {
                return false;
            }
        }
    
        /* 	adapted from http://100days.de/serendipity/archives/55-Dumping-MemcacheD-Content-Keys-with-PHP.html */
        private function get_data( $max_entries = 500, $host, $port = '11211' ) {
            
            $mem = new \Memcache();
            $mem->connect($host, $port);
            
            $list       = array();
            $allSlabs   = $mem->getExtendedStats( 'slabs' );
            $items      = $mem->getExtendedStats( 'items' );
            $data       = array();
            
            foreach( $allSlabs as $server => $slabs ) {
                
                foreach( $slabs as $slabId => $slabMeta ) {
                    
                    $cdump = $mem->getExtendedStats ('cachedump', (int)$slabId, $max_entries );
                    foreach( $cdump as $server => $entries ) {
                        if( $entries ) {
                            
                            foreach( $entries AS $eName => $eData ) {
                                $the_key = esc_attr( $eName );
                                $the_value = esc_attr( $mem->get( $the_key ) );
                                $data[$the_key] =  $the_value;	
                            }
                            
                        }
                    }
                }
            }
            
            return $data;
        }

        
    }
}