<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Memcached Manager</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-1">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						
                        
                        <form action="" method="post">
                            
						<div class="inside">
						
                            <form action="" method="post">
                            <table class="widefat">
                                <tr>
                                    <th>#</th>
                                    <th>Host</th>
                                    
                                    <th>Status</th>
                                    <th>Efficiency</th>
                                    <th>Gets</th>
                                    <th>Sets</th>
                                    <th>Misses</th>
                                    <th>Evictions</th>
                                    <th>Conn</th>
                                    <th>Objects</th>
                                    <th>Size</th>
                                    <th>Uptime</th>
                                    <th>Action</th>
                                    
                                   
                                </tr>
                                
                                <?php foreach($servers as $server): ?>
                                
                                <tr>
                                    <td><?= ++$i; ?></td>
                                    <td><?= $server->host; ?>:<?= $server->port; ?></td>
                            
                                    <td><?= $server->status; ?></td>
                                    <td>
                                        <?php //$server->hits; ?>
                                        <img src="http://chart.apis.google.com/chart?cht=bhs&chs=100x10&chd=t:<?= $server->hitrate; ?>|100&chco=00CC66,CCCCCC&chbh=5" />
                                        <?= $server->hitrate; ?>%
                                        <br />

                                        <?php //$server->misses;?>
                                        <img src="http://chart.apis.google.com/chart?cht=bhs&chs=100x10&chd=t:<?= $server->missrate; ?>|100&chco=FF0000,CCCCCC&chbh=5" />
                                        <?= $server->missrate; ?>%
                                        
                                    </td>
                                    <td><?= $server->gets; ?></td>
                                    <td><?= $server->sets; ?></td>
                                    <td><?= $server->misses; ?></td>
                                    <td><?= $server->evictions; ?></td>
                                    
                                    <td><?= $server->connections; ?></td>
                                    <td><?= $server->objects; ?></td>
                                    <td><?= $server->size; ?></td>
                                    <td><?= $server->uptime; ?></td>
                                    <td>
                                        <?php submit_button( 'Purge', $type = 'delete', $name = 'purge[' . $server->host . ']', $wrap = false, $other_attributes = null ); ?>
                                        <?php submit_button( 'View', $type = 'delete', $name = 'view[' . $server->host . ']', $wrap = false, $other_attributes = null ); ?>
                                    
                                    </td>
                                    
                                </tr>
                                
                                
                                <?php endforeach; ?>
                               
                            </table>
                            <br />
                            
                            <?php submit_button( 'Refresh', $type = 'submit', $name = 'refresh', $wrap = false, $other_attributes = null ); ?>
                            
                            
                        </div> <!-- .inside -->
                        
                        </form>
                            
                        
                    </div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
            
			
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
        
        <?php if($view): ?>
        <div class="metabox-holder columns-1">
            <h3> Viewing <?= $view->server; ?></h3>
            <table class="widefat">
                
                <tr>
                    <td>Key</td>
                    <td>Value</td>
                </tr>
                
                <?php foreach($view->data as $key=>$data): ?>
                <tr>
                    <td><?= $key; ?></td>
                    <td><?= $data; ?></td>
                </tr>
                <?php endforeach; ?>
                    
            </table>
  
        </div>
        <?php endif; ?>
             
            
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->