# wp-memcached-manager
Simple ui for managing memcached clusters in wp. 

in wp-config:
global $memcached_servers;
$memcached_servers = array('127.0.0.1');

slab stuff adapted from:
http://100days.de/serendipity/archives/55-Dumping-MemcacheD-Content-Keys-with-PHP.html
